
<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// ======= 基本設定 =======
const token = 'CWA-66252587-7CC5-4C09-BCED-09C2746EFFF1' // 你的 API 金鑰
const locationName = '仁愛鄉' // 要查詢的鄉鎮
const dailyForecast = ref([]) // 濃縮後的每日預報資料（用於畫面）

// ======= 共用工具 =======
// 從 WeatherElement 陣列找出指定 ElementName 的 Time 陣列（支援中/英欄位名）
function getTimeSeries(elements, names) {
  const matched = elements.find(el => names.includes(el.ElementName))
  return matched?.Time ?? []
}

// 從 ElementValue 陣列取實際值（API 不同版本的 key 可能不同）
function pickElementValue(elementValueArray) {
  if (!Array.isArray(elementValueArray) || elementValueArray.length === 0) return ''
  const obj = elementValueArray[0]
  return obj.Value ?? obj.Weather ?? obj.Temperature ?? obj.Probability ?? Object.values(obj)[0] ?? ''
}

// 解析「天氣預報綜合描述」成結構化欄位（wx/pop/minT/maxT）
function parseWeatherDescription(text) {
  const s = (text || '').replace(/\s+/g, ' ').trim()

  // 天氣現象：取描述開頭直到遇到「溫度/氣溫/最高/最低/降雨…」或標點
  const wxMatch = s.match(/^(.+?)(?=(溫度|氣溫|最高|最低|降雨|降雨率|降雨機率|，|,|。|$))/)
  const wxText = (wxMatch?.[1] || '-').replace(/[，,。]$/, '').trim()

  // 降雨率（整數％）
  const popMatch = s.match(/降雨(?:率|機率)\s*[:：]?\s*(\d{1,3})\s*%?/)
  const popValue = popMatch?.[1] || '-'

  // 溫度範圍
  let minT = '-', maxT = '-'
  const range1 = s.match(/(?:溫度|氣溫)[^0-9\-]*?(-?\d+(?:\.\d+)?)\s*(?:至|-|~|—|–)\s*(-?\d+(?:\.\d+)?)/)
  if (range1) {
    minT = range1[1]
    maxT = range1[2]
  } else {
    const hi = s.match(/最高(?:溫|溫度)?\s*[:：]?\s*(-?\d+(?:\.\d+)?)/)
    const lo = s.match(/最低(?:溫|溫度)?\s*[:：]?\s*(-?\d+(?:\.\d+)?)/)
    if (hi && lo) {
      maxT = hi[1]
      minT = lo[1]
    } else {
      const range2 = s.match(/(-?\d+(?:\.\d+)?)\s*(?:至|-|~|—|–)\s*(-?\d+(?:\.\d+)?)/)
      if (range2) {
        minT = range2[1]
        maxT = range2[2]
      }
    }
  }

  return { wxText, popValue, minT, maxT }
}

// 依天氣現象關鍵字回傳對應 SVG 檔路徑（public/img/weather）
// 你提供的檔案：sunny.svg / cloudy.svg / rain.svg / windy.svg
function resolveIconPathByWx(weatherText) {
  const text = weatherText || ''
  const isThunder = /雷/.test(text)
  const isSnow = /雪|霰/.test(text)
  const isRain = /雨/.test(text)
  const isWindy = /風/.test(text)
  const isCloudy = /雲|陰/.test(text)
  const isSunny = /晴/.test(text)

  // 先判斷較嚴重/明顯的天氣
  if (isThunder || isRain) return '/img/weather/rain.svg'
  if (isSnow) return '/img/weather/rain.svg' // 如果有雪的專用圖，改成 /snow.svg
  if (isWindy) return '/img/weather/windy.svg'
  if (isCloudy) return '/img/weather/cloudy.svg'
  if (isSunny) return '/img/weather/sunny.svg'
  // 預設：多雲
  return '/img/weather/cloudy.svg'
}

// 月份/日期切割（月份英文大寫，日期兩位數）
function splitMonthAndDay(isoDate) {
  const d = new Date(isoDate)
  return {
    monthEN: d.toLocaleString('en-US', { month: 'long' }).toUpperCase(), // e.g., AUGUST
    dayOfMonth: String(d.getDate()).padStart(2, '0'),                    // e.g., 05
  }
}

// ======= 主流程 =======
onMounted(async () => {
  const url =
    `https://opendata.cwa.gov.tw/api/v1/rest/datastore/F-D0047-091?Authorization=${token}&locationName=${locationName}`

  const res = await axios.get(url)
  const elements = res.data.records.Locations[0].Location[0].WeatherElement

  // 來源一：天氣預報綜合描述（優先）
  const descriptionSeries = getTimeSeries(elements, ['天氣預報綜合描述', 'WeatherDescription'])

  // 來源二：單一要素（作為缺值補強）
  const wxSeries   = getTimeSeries(elements, ['天氣現象', 'Wx'])
  const popSeries  = getTimeSeries(elements, ['12小時降雨機率', '降雨機率', 'PoP12h'])
  const minTSeries = getTimeSeries(elements, ['最低溫度', 'MinT'])
  const maxTSeries = getTimeSeries(elements, ['最高溫度', 'MaxT'])

  // 將 12 小時資料濃縮為「每日一筆」：優先取 06:00~18:00；若無則取同日第一筆
  const byDate = new Map()
  const baseCandidates = descriptionSeries.length ? descriptionSeries : wxSeries
  baseCandidates.forEach(timeSlice => {
    const dateISO = timeSlice.StartTime.slice(0, 10)
    const isDaytime = timeSlice.StartTime.includes('06:00')
    const existing = byDate.get(dateISO)
    if (!existing || (isDaytime && !existing.isDaytime)) {
      byDate.set(dateISO, { timeSlice, isDaytime })
    }
  })

  const dateList = Array.from(byDate.keys()).slice(0, 7) // 只取七天

  dailyForecast.value = dateList.map(dateISO => {
    const baseTimeSlice = byDate.get(dateISO).timeSlice

    // 綜合描述解析
    const descriptionText = pickElementValue(baseTimeSlice.ElementValue)
    const parsed = parseWeatherDescription(descriptionText)

    // 同日的單要素備援取值（若解析不到就用單要素）
    const pickSameDayValue = (series) => {
      const sameDay = series.filter(t => t.StartTime.slice(0, 10) === dateISO)
      const day06 = sameDay.find(t => t.StartTime.includes('06:00')) || sameDay[0]
      return day06 ? pickElementValue(day06.ElementValue) : ''
    }

    const wxText = parsed.wxText !== '-' ? parsed.wxText : (pickSameDayValue(wxSeries) || '-')

    // 降雨率顯示需求：如果沒有資料就顯示「超出範圍」
    let popText = parsed.popValue !== '-' ? parsed.popValue + '%' : (pickSameDayValue(popSeries) || '-')
    if (popText === '-') popText = '尚未偵測'

    const minText = parsed.minT !== '-' ? parsed.minT : (pickSameDayValue(minTSeries) || '-')
    const maxText = parsed.maxT !== '-' ? parsed.maxT : (pickSameDayValue(maxTSeries) || '-')

    const { monthEN, dayOfMonth } = splitMonthAndDay(dateISO)
    const iconPath = resolveIconPathByWx(wxText)

    return {
      monthEN,          // e.g., AUGUST
      dayOfMonth,       // e.g., 05
      wx: wxText,       // 天氣現象文字
      popDisplay: popText, // "30%" or "超出範圍"
      minT: minText,
      maxT: maxText,
      iconPath,         // 對應的 SVG 路徑
    }
  })
})
</script>


<template>
  <div class="weeklyWrapper"> 
    <div class="weekly"><!--1200px-->
      <h3>一週天氣</h3>
      <span>Weekly Weather</span>
      <ul>
        <li
          v-for="(day, index) in dailyForecast"
          :key="index"
          class="dayCard"
        >
          <div class="date">
            <p>{{ day.monthEN }}</p>
            <span>{{ day.dayOfMonth }}</span>
          </div>

            <!-- 天氣icon -->
          <img
            class="icon"
            :src="day.iconPath"
            alt="weather icon"
            width="44"
            height="44"
          />
          <div class="hotORrain">
            <p>溫度：{{ day.minT }}° / {{ day.maxT }}°</p>
            <p>降雨：{{ day.popDisplay }}</p>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>


<style>
 body{

 }
</style>

<style lang="scss" scoped>
@import '../../assets/styles/main.scss';

.weeklyWrapper{
  background-color: rgba(255, 255, 255, 0.5);
}

.weekly{
  // border: 1px solid red;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 30px 80px;
  border-radius: 16px;
  

  h3{
    font-size: $pcFont-H2;
    font-weight: $semiBold;
    letter-spacing: 2px;
    line-height: $lineHeight-title-120;
    margin-left: 16px;
  }


  >span{
    margin-left: 16px;

    
    line-height: $lineHeight-p-150;
    font-weight: $medium;

    font-size: 16px;
    letter-spacing: 0.7px;

  }
  
  ul{
    display: flex;
    margin-top: 48px;
    

  

    .dayCard{ /* li */ 
      // border: 1px solid blue;
      flex-basis: 0;
      flex-grow: 1;
      padding: 0 30px;


      display: flex;
      flex-direction: column;
      gap: 12px;

      .date{ /* 日期框 */

        text-align: center;
        line-height: $lineHeight-p-150;
        p{ /* 月份 */
          font-size: 10px;
          font-weight: $semiBold;
        }

        span{ /* 日期 */
          font-size: $pcFont-H2;
          font-weight: 900;
          letter-spacing: 1px;
        
        }

      }

      .icon{ /* img */
        display: block;
        margin: 0 auto;
        height: 50px;
        width: auto;

      }

      .hotORrain{ /* 溫度降雨率 */
        line-height: $lineHeight-p-150;
        font-size: 14px;
        

      }



    }
  }
}

</style>
