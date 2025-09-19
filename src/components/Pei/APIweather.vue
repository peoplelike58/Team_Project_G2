
<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// ====== 父層傳入：預報地區 ======
const props = defineProps({
  town: { type: String, required: true }
})
const town = props.town

// ====== 路徑基準（子目錄部署會自動帶上） ======
const baseUrl = import.meta.env.BASE_URL

// ====== 畫面資料 ======
const dailyForecast = ref([]) // 濃縮為每日一筆，共 7 天

// ====== 共用工具 ======
function getTimeSeries(elements, names) {
  const matched = elements.find(el => names.includes(el.ElementName))
  return matched?.Time ?? []
}
function pickElementValue(arr) {
  if (!Array.isArray(arr) || arr.length === 0) return ''
  const obj = arr[0]
  return obj.Value ?? obj.Weather ?? obj.Temperature ?? obj.Probability ?? Object.values(obj)[0] ?? ''
}
function parseWeatherDescription(text) {
  const s = (text || '').replace(/\s+/g, ' ').trim()
  const wxMatch = s.match(/^(.+?)(?=(溫度|氣溫|最高|最低|降雨|降雨率|降雨機率|，|,|。|$))/)
  const wxText = (wxMatch?.[1] || '-').replace(/[，,。]$/, '').trim()
  const popMatch = s.match(/降雨(?:率|機率)\s*[:：]?\s*(\d{1,3})\s*%?/)
  const popValue = popMatch?.[1] || '-'
  let minT = '-', maxT = '-'
  const range1 = s.match(/(?:溫度|氣溫)[^0-9\-]*?(-?\d+(?:\.\d+)?)\s*(?:至|-|~|—|–)\s*(-?\d+(?:\.\d+)?)/)
  if (range1) {
    minT = range1[1]; maxT = range1[2]
  } else {
    const hi = s.match(/最高(?:溫|溫度)?\s*[:：]?\s*(-?\d+(?:\.\d+)?)/)
    const lo = s.match(/最低(?:溫|溫度)?\s*[:：]?\s*(-?\d+(?:\.\d+)?)/)
    if (hi && lo) { maxT = hi[1]; minT = lo[1] }
    else {
      const range2 = s.match(/(-?\d+(?:\.\d+)?)\s*(?:至|-|~|—|–)\s*(-?\d+(?:\.\d+)?)/)
      if (range2) { minT = range2[1]; maxT = range2[2] }
    }
  }
  return { wxText, popValue, minT, maxT }
}
function splitMonthAndDay(isoDate) {
  const d = new Date(isoDate)
  return {
    monthEN: d.toLocaleString('en-US', { month: 'short' }).toUpperCase(),
    dayOfMonth: String(d.getDate()).padStart(2, '0'),
  }
}

// 天氣 → 本地 SVG 路徑（public/img/weather/*.svg）
function resolveIconPathByWx(weatherText) {
  const text = weatherText || ''
  const isThunder = /雷/.test(text)
  const isSnow    = /雪|霰/.test(text)
  const isRain    = /雨/.test(text)
  const isWindy   = /風/.test(text)
  const isCloudy  = /雲|陰/.test(text)
  const isSunny   = /晴/.test(text)

  let name = 'cloudy'
  if (isThunder || isRain) name = 'rain'
  else if (isSnow)         name = 'rain'   
  else if (isWindy)        name = 'windy'
  else if (isCloudy)       name = 'cloudy'
  else if (isSunny)        name = 'sunny'

  return `${baseUrl}images/Weather/${name}.svg`
}

// ====== 主流程：抓氣象署 7 日資料 ======
onMounted(async () => {
  const token = 'CWA-66252587-7CC5-4C09-BCED-09C2746EFFF1'
  const url =
    `https://opendata.cwa.gov.tw/api/v1/rest/datastore/F-D0047-091?Authorization=${token}&locationName=${encodeURIComponent(town)}`

  const { data } = await axios.get(url)
  const locations = data.records?.Locations?.[0]?.Location || []
  const location = locations.find(item => item.LocationName === town)
  if (!location) { dailyForecast.value = []; return }

  const elements   = location.WeatherElement
  const descSeries = getTimeSeries(elements, ['天氣預報綜合描述', 'WeatherDescription'])
  const wxSeries   = getTimeSeries(elements, ['天氣現象', 'Wx'])
  const popSeries  = getTimeSeries(elements, ['12小時降雨機率', '降雨機率', 'PoP12h'])
  const minTSeries = getTimeSeries(elements, ['最低溫度', 'MinT'])
  const maxTSeries = getTimeSeries(elements, ['最高溫度', 'MaxT'])

  // 以 06:00~18:00 為優先，濃縮成每日一筆
  const byDate = new Map()
  const baseCandidates = descSeries.length ? descSeries : wxSeries
  baseCandidates.forEach(ts => {
    const dateISO   = ts.StartTime.slice(0, 10)
    const isDaytime = ts.StartTime.includes('06:00')
    const exist = byDate.get(dateISO)
    if (!exist || (isDaytime && !exist.isDaytime)) byDate.set(dateISO, { timeSlice: ts, isDaytime })
  })

  const dateList = Array.from(byDate.keys()).slice(0, 7)

  const pickSameDayValue = (series, dateISO) => {
    const same = series.filter(t => t.StartTime.slice(0, 10) === dateISO)
    const day06 = same.find(t => t.StartTime.includes('06:00')) || same[0]
    return day06 ? pickElementValue(day06.ElementValue) : ''
  }

  dailyForecast.value = dateList.map(dateISO => {
    const baseTimeSlice  = byDate.get(dateISO).timeSlice
    const descText       = pickElementValue(baseTimeSlice.ElementValue)
    const parsed         = parseWeatherDescription(descText)

    const wxText = parsed.wxText !== '-' ? parsed.wxText : (pickSameDayValue(wxSeries, dateISO) || '-')

    let popText = parsed.popValue !== '-' ? `${parsed.popValue}%` : (pickSameDayValue(popSeries, dateISO) || '-')
    if (popText === '-') popText = '尚未偵測'

    const minText = parsed.minT !== '-' ? parsed.minT : (pickSameDayValue(minTSeries, dateISO) || '-')
    const maxText = parsed.maxT !== '-' ? parsed.maxT : (pickSameDayValue(maxTSeries, dateISO) || '-')

    const { monthEN, dayOfMonth } = splitMonthAndDay(dateISO)

    return {
      monthEN,
      dayOfMonth,
      wx: wxText,
      popDisplay: popText,
      minT: minText,
      maxT: maxText,
      iconPath: resolveIconPathByWx(wxText),
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
            class="weather-icon"
            :src="day.iconPath"
            alt="weather icon"
            width="44"
            height="44"
          />
          <div class="hotORrain">
            <p>溫度：{{ day.minT }}° ～ {{ day.maxT }}°</p>
            <p>降雨：{{ day.popDisplay }}</p>
          </div>
        </li>
      </ul>

      <div class="showTown">
        <p>資料來源：氣象署｜預報地區：{{ town }}</p>
      </div>
    </div>
  </div>
</template>


<style>
 body{

 }
</style>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/mixins';

.weeklyWrapper{
  background-color: rgba(255, 255, 255, 0.5);
}

.weekly{
  // border: 1px solid red;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 30px 50px;
  border-radius: 16px;
  box-sizing: border-box;

  @include m(){
    max-width:768px ;
    padding: 30px 20px;
    font-size: 14px;
  }
  

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

    @include m(){
      flex-direction: column;
      gap:28px;
      max-width: 70%;
      margin: 48px auto ;
    }
    
    @media (max-width: 655px){
      max-width: 80%;

    }

    @include s(){
      max-width: 90%;

    }

    @media (max-width: 390px){
      max-width: 100%;

    }

  

    .dayCard{ /* li */ 
      // border: 1px solid blue;
      flex-basis: 0;
      flex-grow: 1;
      padding: 0 30px;


      display: flex;
      flex-direction: column;
      gap: 12px;

      @include m(){
        flex-direction: row;
        gap:8px;
        
      }

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

      .weather-icon{ /* img */
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
  
  .showTown{
  // border: 1px solid rebeccapurple;
  text-align: right;
  margin-top: 48px;
  font-size: 14px;
  color: #999;
  letter-spacing: 1px;
  width: 100%;
  @include m(){
    max-width: 728px;
  }

  }


}

</style>