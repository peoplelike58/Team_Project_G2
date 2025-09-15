<template>
  <div class="peaktitle">
      <h1>{{ peakTitle }}</h1>
      <p>{{ peakSubTitle }}</p>
  </div>

  <div class="peak-map">
    <l-map
      ref="mapRef"
      :center="[23.7, 121]"
      :zoom="8.5"                
      :minZoom="7"
      :maxZoom="8.5"
      :zoomControl="false"
      :scrollWheelZoom="false"  
      :doubleClickZoom="false"
      :touchZoom="false"
      :dragging="false"    
      :boxZoom="false"
      :keyboard="false"
      :max-bounds="[[21,119],[26,123]]"
      :maxBoundsViscosity="1"
      style="height: 100%; width: 100%;"
    >
    <!-- 引用地圖 -->
    <l-tile-layer
        url="https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}"
        attribution="Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community"
      />

    <!-- 旗幟  -->
    <l-marker
      v-for="mountain in mountains"
      :key="mountain.id"
      :lat-lng="mountain.coord"
      :icon="flagIcon"
      @click="openOverlay(mountain)"  
    />
  </l-map>

    <!-- click 時觸發（不用leaflet內建彈窗） -->
    <!-- 彈窗(卡片設定置中不隨著旗幟)  -->
    <div v-if="mountain" class="map-overlay" @click.self="closeOverlay">
      <div class="map-card">
        <button class="close-btn" @click="closeOverlay">
            <span>x</span>
        </button>
         <!-- 上方地圖 -->
         <div class="map-section">
            <img 
                :src="mountain.mapImage" 
                :alt="`${mountain.name} 地圖`"
                class="map-image" />
         </div>
          <hr>
          <!-- 百岳資訊區域 -->
          <div class="mountain-info">
              <h2 class="mountain-name">{{ mountain.name }}</h2>
              <span class="elevation">{{ mountain.height }}</span>
          </div>
          <hr class="dashed">
          <!-- 百岳描述 -->
          <div class="mountain-details">
              <span>{{ mountain.description }}</span>
              <p v-if="mountain.originalName">原住民語：{{ mountain.originalName }}</p>
              <p >英文名：{{ mountain.englishName }}</p>
              <p >特色：{{ mountain.features }}</p>
              <p >所在地：{{ mountain.location }}</p>
              <hr class="line">
          </div>
          <!-- View Details Button -->
          <div class="btn-box">
              <button class="btn" @click="openEbook(mountain)">
                  <span>View Detail</span>
                  <div class="icon">↗</div>
              </button>
          </div>
       </div>
     </div>
  </div>

    <!-- 引用電子書子組件 -->
    <PeakEBook 
    v-if="showEbook" 
    :mountain="selectedMountain" 
    @close="closeEbook" 
  />
</template>

<script setup>
  import { ref, onMounted, h } from "vue";
  //引用 leaflet for vue plugin 的 css, js
  import 'leaflet/dist/leaflet.css'
  import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet'
  import L from 'leaflet'
  import PeakEBook from "@/components/PeakEBook.vue";

  // import yushanMap from '@/assets/images/MapOfMountain/Yushan.png'
  // import yushanFull from '@/assets/images/PeaksOfTaiwan/Yushan/Yushan_full.jpg'
  // import yushanLeft from '@/assets/images/PeaksOfTaiwan/Yushan/Yushan_left.jpg'
  // import yushanRight from '@/assets/images/PeaksOfTaiwan/Yushan/Yushan_right.jpg'

  // import xueshanMap from '@/assets/images/MapOfMountain/Xueshan.png'
  // import xueshanFull from '@/assets/images/PeaksOfTaiwan/Xueshan/Xueshan_full.png'
  // import xueshanLeft from '@/assets/images/PeaksOfTaiwan/Xueshan/Xueshan_left.jpg'
  // import xueshanRight from '@/assets/images/PeaksOfTaiwan/Xueshan/Xueshan_right.jpg'

  // import xiuguluanMap from '@/assets/images/MapOfMountain/Xiuguluan.png'
  // import xiuguluanFull from '@/assets/images/PeaksOfTaiwan/Xiuguluan/Xiuguluan_full.png'
  // import xiuguluanLeft from '@/assets/images/PeaksOfTaiwan/Xiuguluan/Xiuguluan_left.jpg'
  // import xiuguluanRight from '@/assets/images/PeaksOfTaiwan/Xiuguluan/Xiuguluan_right.jpg'

  // import hehuanMap from '@/assets/images/MapOfMountain/Hehuan.png'
  // import hehuanFull from '@/assets/images/PeaksOfTaiwan/Hehuan/Hehuan_full.png'
  // import hehuanLeft from '@/assets/images/PeaksOfTaiwan/Hehuan/Hehuan_left.jpg'
  // import hehuanRight from '@/assets/images/PeaksOfTaiwan/Hehuan/Hehuan_right.jpg'

  // import pintianMap from '@/assets/images/MapOfMountain/Pintian.png'
  // import pintianFull from '@/assets/images/PeaksOfTaiwan/Pintian/Pintian_full.png'
  // import pintianLeft from '@/assets/images/PeaksOfTaiwan/Pintian/Pintian_left.jpg'
  // import pintianRight from '@/assets/images/PeaksOfTaiwan/Pintian/Pintian_right.jpg'

  // import dabaMap from '@/assets/images/MapOfMountain/Daba.png'
  // import dabaFull from '@/assets/images/PeaksOfTaiwan/Dabajianshan/Daba_full.png'
  // import dabaLeft from '@/assets/images/PeaksOfTaiwan/Dabajianshan/Daba_left.jpg'
  // import dabaRight from '@/assets/images/PeaksOfTaiwan/Dabajianshan/Daba_right.jpg'

  // import qilaiMap from '@/assets/images/MapOfMountain/Qilai.png'
  // import qilaiFull from '@/assets/images/PeaksOfTaiwan/Qilai/Qilai_full.png'
  // import qilaiLeft from '@/assets/images/PeaksOfTaiwan/Qilai/Qilai_left.jpg'
  // import qilaiRight from '@/assets/images/PeaksOfTaiwan/Qilai/Qilai_right.jpg'

  // import nanhuMap from '@/assets/images/MapOfMountain/Nanhu.png'
  // import nanhuFull from '@/assets/images/PeaksOfTaiwan/Nanhu/Nanhu_full.png'
  // import nanhuLeft from '@/assets/images/PeaksOfTaiwan/Nanhu/Nanhu_left.jpg'
  // import nanhuRight from '@/assets/images/PeaksOfTaiwan/Nanhu/Nanhu_right.jpg'

  // import guanshanMap from '@/assets/images/MapOfMountain/Guanshan.png'
  // import guanshanFull from '@/assets/images/PeaksOfTaiwan/Guanshan/Guanshan_full.png'
  // import guanshanLeft from '@/assets/images/PeaksOfTaiwan/Guanshan/Guanshan_left.jpg'
  // import guanshanRight from '@/assets/images/PeaksOfTaiwan/Guanshan/Guanshan_right.jpg'

  // import nenggaoMap from '@/assets/images/MapOfMountain/Nenggao.png'
  // import nenggaoFull from '@/assets/images/PeaksOfTaiwan/Nenggao/Nenggao_full.png'
  // import nenggaoLeft from '@/assets/images/PeaksOfTaiwan/Nenggao/Nenggao_left.jpg'
  // import nenggaoRight from '@/assets/images/PeaksOfTaiwan/Nenggao/Nenggao_right.jpg'
  //先載入PageFlip 再載入地圖

  // const pageFliploaded = ref(false);
  // const mapReady = ref(false);

  // onMounted(() => {
  //   initializePageFlip();
  //   setTimeout(() => {
  //     pageFliploaded.value = true;
      
  //     setTimeout(()=>{
  //       mapReady.value =true;
  //     }, 500)
  //   }, 1000)
  // })


  // Peak Title
  const peakTitle = "百岳精選"
  const peakSubTitle = "十大傳奇山峰"

  //引用 leaflet map 實例
  const mapRef = ref(null)
  //真正存取的地方
  const map = ref(null)
  //元件掛載之後 取值
  onMounted(() => {
    map.value = mapRef.value?.leafletObject || null
  })



  // const popupOpts = {
  //   autoPan: true,
  //   autoPanPadding: [40, 40],
  //   keepInView: false, 
  //   closeButton: true     
  // }
  // 自訂旗幟

//   const testIcon = L.divIcon({
//   className: 'mountain-flag',
//   html: '<div style="font-size:26px;line-height:1">🚩</div>',
//   iconSize: [30, 30],
//   iconAnchor: [13, 26],
// })


const flagIcon = L.divIcon({
  className: 'leaflet-div-icon mountain-flag', // 保留 leaflet-div-icon
  html: '<div style="font-size:26px;line-height:1">🚩</div>',
  iconSize: [36, 36],
  iconAnchor: [13, 26],
  popupAnchor: [0, -26],
});

//Overlay狀態
const mountain = ref(null)
const openOverlay = (m) => {
  mountain.value = m
  if (map.value){
    map.value.setView(m.coord, map.value.getZoom(), { animate: false })
  }
}
const closeOverlay = () => {
  mountain.value = null
}

  // const flagIcon =  L.icon({
  //     iconUrl: '/images/icon/icon-flag.png',
  //     iconSize:[30, 60],
  //     iconAnchor: [0, 60],  // 定位點 (x, y)
  //     popupAnchor: [0, -60]     //彈窗位置
  // })

  // E book
  const showEbook = ref(false)           // 是否顯示電子書
  const selectedMountain = ref(null)     // 目前選中的百岳

  const openEbook = (mountain) => {
    selectedMountain.value = mountain
    showEbook.value = true
  }

  const closeEbook = () => {
    showEbook.value = false
    selectedMountain.value = null
  }

 //響應式設定
  const screenWidth = ref(window.innerWidth)
  

  

  // 山峰資料
  const mountains = ref([
  {
    id: 1,
    name: '玉山',
    eName:'Yushan',
    height: '3,952m',
    location: '南投縣',
    description: '台灣最高峰，東北亞最高峰',
    originalName: 'Pattonkan (布農語)',
    englishName: 'Yushan Mountain',
    features: '台灣第一高峰，擁有豐富的高山生態系統',
    coord: [23.4697, 120.9576],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Yushan.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Yushan/Yushan_full.jpg`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Yushan/Yushan_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Yushan/Yushan_right.jpg`,
    slogan:'海拔3952公尺的凝視',
    subSlogan:'的時間、風與靜默',
    title:'地之高，心之遠',
    content1: '玉山，舊稱新高山，是台灣最高峰，海拔3,952公尺，也是東北亞第一高峰，位於南投、嘉義、高雄三縣市交界，雄踞於玉山山脈之巔。其主峰高大陡峭，四面多為險峻岩壁，三角點位於其頂端，是廣受登山者敬仰的「百岳之王」',
    title2:'主峰日出，一秒千年',
    content2: '登頂的那一刻，並不喧嘩，反而無聲勝有聲。當陽光染紅岩壁，遠方中央尖山浮現輪廓，你不再只是拍攝者，而成了山的一部份。'

  },
  {
    id: 2,
    name: '雪山',
    eName:'Xueshan',
    height: '3,886m',
    location: '南投縣、中部',
    description: '在雪與雲之間，仰望寂靜的峻岭',
    originalName: 'Sekoan（泰雅族語）',
    englishName: 'Xueshan Main Peak',
    features: '雲海仙境，黑森林步道聞名，適合新手挑戰',
    coord: [24.4806, 121.2500],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Xueshan.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Xueshan/Xueshan_full.png`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Xueshan/Xueshan_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Xueshan/Xueshan_right.jpg`,
    slogan:'海拔3886公尺的凝目',
    subSlogan:'時光、雲霞與寧靜',
    title:'天際廣闊，心境無垠',
    content1: '雪山，台灣第二高峰，海拔3,886公尺，為雪山山脈最高峰，位於台中、苗栗交界，擁有全台最大、最完整的冰河遺跡。壯闊的山谷與清晰山稜線交織，兼具原始森林與冰河地形的美態，是台灣「五嶽」之一，擁有豐富多樣的自然生態。',
    title2:'峰頂日出，秒盡千秋',
    content2: '當光芒穿透雲霧，映照岩峰與森林，你不僅是攀登者，更成為這片自然史詩的見證者。'
  },
  {
    id: 3,
    name: '秀姑巒山',
    eName:'Xiuguluan',
    height: '3,805m',
    location: '南投縣',
    description: '東岸最高，優雅如畫的孤峰',
    originalName: 'Kangkuwan (阿美族語)',
    englishName: 'Xiuguluan Mountain',
    features: '台灣第三高峰，擁有高山箭竹與異國風野',
    coord: [23.496791, 121.062309],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Xiuguluan.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Xiuguluan/Xiuguluan_full.png`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Xiuguluan/Xiuguluan_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Xiuguluan/Xiuguluan_right.jpg`,
    slogan:'地標的3805公尺',
    subSlogan:'與風與霧與安然共舞',
    title:'天地遼闊，心亦遠',
    content1: '秀姑巒山，海拔3,866公尺，座落於花蓮與南投交界，氣候多變，雲霧繚繞，深具原始感。秀姑巒溪源頭自此，生態多樣且豐富，展現東部山脈的靜謐與力量。',
    title2:'晨曦破霧，超越瞬間',
    content2: '旭日初升，皎潔光暈映照著霧氣繚繞的山巒，靜謐中，山與風共舞，時間在此刻凝聚。'
  },
  {
    id: 4,
    name: '合歡主峰',
    eName:'Hehuan',
    height: '3,417m',
    location: '南投縣',
    description: '雪白世界的門扉，山旅的起點',
    originalName: '',
    englishName: 'Hehuan Main Peak',
    features: '新手友善，擁有高山花海與日出美景',
    coord: [24.1420, 121.2720],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Hehuan.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Hehuan/Hehuan_full.png`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Hehuan/Hehuan_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Hehuan/Hehuan_right.jpg`,
    slogan:'站在海拔3417的視界',
    subSlogan:'風語與時光的低語',
    title:'心隨山高，境與天長',
    content1: '合歡主峰，位於中央山脈南端，擁有3,416公尺海拔，四季分明，冬季積雪豐富，是台灣少數能輕鬆賞雪的高山。緩坡適合多元登山體驗，山嶺間草原綠意盎然。',
    title2:'陽光初綻，凝固序幕',
    content2: '陽光灑落合歡峰巒，清風吹拂草原，時光的靜止與自然的律動在這裡交匯。'
  },
  {
    id: 5,
    name: '品田山',
    eName:'Pintian',
    height: '3,668m',
    location: '新竹縣與台中市交界',
    description: '鋸齒天際線，最野性的稜脈之歌',
    originalName: '',
    englishName: 'Pintian Mountain',
    features: '聖稜線群峰之一，奇岩峭壁極具挑戰性',
    coord: [24.4667, 121.1417],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Pintian.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Pintian/Pintian_full.png`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Pintian/Pintian_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Pintian/Pintian_right.jpg`,
    slogan:'高聳於3668公尺高空',
    subSlogan:'風、光與寂然的對話',
    title:'峰高心遠，境拓思深',
    content1: '品田山，海拔3,668公尺，為雪山山脈的重要峰頂之一，自然生態豐富，生物多樣性高，山峰輪廓宏偉壯闊，為環境保護區重點。',
    title2:'峰頂霞光，瞬息千載',
    content2: '曙光映照品田山巒，山風輕抚，時間彷彿在此安住，與大地同納永恆。'
  },
  {
    id: 6,
    name: '大霸尖山',
    eName:'Dabajianshan',
    height: '3,492m',
    location: '南投縣、中部',
    description: '仰望圖騰，傳說中的聖稜之心',
    originalName: 'Papak Waqa（賽夏族語）',
    englishName: 'Dabajianshan',
    features: '霸氣十足的岩峰，聖稜線聞名，被譽為「聖山」',
    coord: [24.4000, 121.3500],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Daba.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Dabajianshan/Daba_full.png`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Dabajianshan/Daba_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Dabajianshan/Daba_right.jpg`,
    slogan:'海拔3492公尺的磅礡',
    subSlogan:'石與風的語言',
    title:'地之力，心之堅',
    content1: '大霸尖山，位於雪山山脈聖稜線北端，海拔3,492公尺，以其桶狀岩峰雄偉矗立，有「世紀奇峰」之美譽。岩層節理發達，斷崖陡峭，是泰雅與賽夏族的聖山，展現自然的鋼鐵意志與力量。',
    title2:'主峰曦光，千年不歇',
    content2: '當晨光攀上堅硬砂岩，山巒沉默中迸發生命力，風穿越崖腳，彷彿天地故事低語。你成為這片不朽土地的守護者，見證風與石的詩篇。'

  },
  {
    id: 7,
    name: '奇萊主峰',
    eName:'Qilai',
    height: '3,560m',
    location: '花蓮縣',
    description: '黑色稜線之上，踏入神祕與壯麗',
    originalName: 'Parusan（太魯閣族語）',
    englishName: 'Qilai Main Peak',
    features: '雲霧之王，擁有險峻稜線與高山草原',
    coord: [24.3, 121.2],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Qilai.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Qilai/Qilai_full.png`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Qilai/Qilai_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Qilai/Qilai_right.jpg`,
      slogan:'3560海拔的凝視',
    subSlogan:'時光流影，風吟幽靜',
    title:'地的崢嶸，心的馳宇',
    content1: '奇萊主峰位於中央山脈北段，海拔3,560公尺，以陡峭岩壁與多變氣候著稱，稱為「黑色奇萊」。此峰險峻、技術性高，是登山者嚮往的挑戰之一。',
    title2:'地的崢嶸，心的馳宇',
    content2: '晨光穿越雲霧，時間與風凝聚於峰巔，攀登者在此與山脈融合。'  
  },
  {
    id: 8,
    name: '南湖大山',
    eName:'Nanhu',
    height: '3,742m',
    location: '花蓮縣',
    description: '帝王之山，屹立太魯閣的靈魂',
    originalName: 'Pisayhe（泰雅族語）',
    englishName: 'Nanhu Mountain',
    features: '山形近似富士山，擁有高山湖泊與星空',
    coord: [24.6908, 121.325],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Nanhu.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Nanhu/Nanhu_full.png`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Nanhu/Nanhu_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Nanhu/Nanhu_right.jpg`,
    slogan:'峰巔雕刻的3742高度',
    subSlogan:'時間的流影，風的吟唱',
    title:'心隨山高，境與天長',
    content1: '南湖大山，中央山脈中段，海拔3,742公尺，環繞原始森林，是生態豐富的重要保護區，以其寧靜與壯麗自然美景聞名。',
    title2:'光束初照，超越瞬間',
    content2: '晨曦灑落雲巒，微風輕拂，時間彷彿停駐在這份純淨與壯觀中。' 
  },
  {
    id: 9,
    name: '關山',
    eName:'Guanshan',
    height: '3,668m',
    location: '台東、高雄交界',
    description: '遼闊南橫之巔，藏著萬千層次的雲',
    originalName: '',
    englishName: 'Guanshan',
    features: '南台灣名峰，擁有開闊稜線與夕陽美景',
    coord: [23.2278, 120.9114],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Guanshan.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Guanshan/Guanshan_full.png`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Guanshan/Guanshan_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Guanshan/Guanshan_right.jpg`,
    slogan:'3668的凝目',
    subSlogan:'風起時，靜默與歲月相陪',
    title:'天地遼闊，心亦遠',
    content1: '關山，海拔3,668公尺，位於中央山脈，地形險峻，稜線綿延，承載豐富的自然與人文歷史，是探險者喜愛之地。',
    title2:'朝陽一現，秒盡千秋',
    content2: '光影穿透樹梢，風聲低唱，你與這片古老山林的故事在此交匯。' 
  },
  {
    id: 10,
    name: '能高主鋒',
    eName:'Nenggao',
    height: '3,668m',
    location: '南投縣',
    description: '從古道走來，穿越時光的山影',
    originalName: '',
    englishName: 'Nenggao Main Peak',
    features: '具歷史意義的越嶺古道，擁有雲海與杉木林景觀',
    coord: [ 24.2, 121.0],
    mapImage: `${import.meta.env.BASE_URL}images/MapOfMountain/Nenggao.png`,
    fullImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Nenggao/Nenggao_full.png`,
    leftImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Nenggao/Nenggao_left.jpg`,
    rightImage: `${import.meta.env.BASE_URL}images/PeaksOfTaiwan/Nenggao/Nenggao_right.jpg`,
    slogan:'海拔3668的巔峰凝視',
    subSlogan:'時與風的低語，靜謐的映照',
    title:'地的崢嶸，心的馳宇',
    content1: '能高主峰，坐落於中央山脈，海拔3,668公尺，以廣闊的高山草原與多樣生態聞名，山勢雄偉且氣候多變，是登山路線中的熱門焦點。',
    title2:'日光初綻，超越瞬間',
    content2: '當陽光普照高山草原，微風輕拂，時間凝聚成永恆的印記。' 
  }
])
  
</script>


<style scoped lang="scss">
  @import '@/assets/styles/mixins.scss';
  @import '@/assets/styles/main.scss';
  *{
    box-sizing: border-box;
  }
  .peak-map{
    height: 1080px; 
    width: 780px; 
    position: relative;
    @include m(){
      height: 60vh;
      width: 100%;
      padding: 0 16px;
    }

  }
  .peaktitle{
    margin-top: 12px;
    margin-left: 48px;
    @include m(){
      margin-left: 16px;
      margin-top: 8px;
    }
    h1{
      font-size: $pcFont-bigTitle-m;
      font-weight: $bold;
      line-height: $lineHeight-title-120;
      @include m(){
          font-size:$pcFont-H1-m;
        }
  }
    p{
          font-size: $pcFont-H1-m;
          line-height: $lineHeight-p-200;
          @include m(){
          font-size:$pcFont-H4;

        }
      }
  }
  .peak-map{
    margin: 0 auto;

  }


</style>

<style>
.map-section{
  width: 100%;
  height: 200px;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  margin: 0 auto;
    img{
      max-width: 100%;
      min-height: auto;
      object-fit: contain;
      padding-top: 12px;
    }
}



.map-overlay{
  position:absolute; 
  inset:0;
  display:flex; 
  align-items:center; 
  justify-content:center;
  z-index:1200;
}
.map-card{
  position: relative;
  font-family: 'Inter', sans-serif;
  color: #141414;
  font-size: 16px; 
  line-height: 150%;
  width:460px; 
  /* max-width:calc(100% - 32px); */
  background:#fff; 
  padding:24px;
  box-shadow:0 8px 32px rgba(0,0,0,.2);
  .close-btn{
    position: absolute;
    top: 4px;
    right: 4px;
    background: none !important;  
    border: 0 !important;
    font-size: 24px;
    cursor: pointer;
  }
  @media screen and (max-width: 430px) {
    width: 350px;
    font-size: 14px;
  }
}

.mountain-info{
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  h2{
    padding-left: 12px;
    font-size: 40px;
    font-weight: 700;
    @media screen and (max-width: 430px) {
    font-size: 34px;
  }
  }
  span{
    padding-right: 12px;
    font-size: 20px;
    font-weight: 700;

  }
}
.dashed{
    border: none;              
    border-top: 1px dashed #666; 
    margin: 20px 0; 
    @media screen and (max-width: 430px) {
    margin: 10px 0; 

  }

  }
.mountain-details{
  span{
    padding-left: 12px;
    font-size: 24px;
    font-weight: 500;
    line-height: 200%;
     margin-bottom: 12px;
     @media screen and (max-width: 430px) {
    font-size: 20px;
    line-height: 150%;
  }
  } 
  p{
    padding-left: 12px;
    line-height: 200%;
    @media screen and (max-width: 430px) {
    font-size: 14px;
    line-height: 200%;
  }
  }
}
.line{
  @media screen and (max-width: 430px) {
    display: none;;
  }
}
.btn-box{
  display: flex;
  @media screen and (max-width: 430px) {
    display: none;;
  }
}
.btn {
  margin-left: auto;
  padding-top: 12px;
  display: flex;
  justify-content: space-between; /* 左邊文字 + 右邊箭頭 */
  align-items: flex-end;
  width: 120px;            
  background: transparent;
  color: #141414;           
  border: none;           
  cursor: pointer;
  font-size: 14px;
  span{
    line-height: 1.2;
  }

}

.icon {
  width: 30px;
  height: 30px;
  display: flex;
  align-items: flex-end;
  background-color: #262626;
  color: white;
  font-size: 14px;
  margin-left: 8px;
}

.leaflet-div-icon.mountain-flag{
  background: transparent !important;
  border: 0 !important;
  box-shadow: none !important;
}

.leaflet-container img.leaflet-tile {
  max-width: none !important;
  max-height: none !important;
  /* 不要改 width/height，交給 Leaflet 自己算 */
}

</style>