<template>
    <div class="modalOverlay" @click.self="$emit('closeUploadModal', props.mountain.name)">
        <section class="mychallengeModal">
            <button class="closeBtn" @click="$emit('closeUploadModal', props.mountain.name)">x</button>
            <article class="gpx">
                <div class="mountainInfo">
                    <h2>{{ mountain.name }}</h2>
                    <h4>[{{ mountain.kind }}]</h4>
                </div>
                <div class="uploadArea"
                    @dragover.prevent="onDragOver" 
                    @dragleave.prevent="onDragLeave" 
                    @drop.prevent="onDrop"
                    :class="{ dragover: isDragOver }"
                >
                    <div class="add">
                        <span v-if="fileName">已選擇檔案：<br />{{ fileName }}</span>
                        <span v-else>+</span>
                    </div>
                    <input type="file" id="theFile"
                    @change="onFileChange"
                    >
                </div>
                <div class="score">
                    <div>
                        <p>累積高度</p>
                        <p><span>{{ height }}</span> m</p>
                    </div>
                    <div>
                        <p>累積里程</p>
                        <p><span>{{ kilo }}</span> km</p>
                    </div>
                    <div>
                        <p>累積時間</p>
                        <p><span>{{ time }}</span> hr</p>
                    </div>
                </div>
            </article>
            <article class="think">
                <h4>想法記錄</h4>
                <textarea
                    v-model="thought"
                    maxlength="500"
                    @input="updateCount"
                ></textarea>
                <p class="textCount">{{ textCount }} / 500</p>
            </article>
            <div class="buttunWrapper">
                <button @click="saveThought">提交</button>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
// import { useRecordStore } from "@/stores/recordStore"
import axios from 'axios'

    const height = ref(0)
    const kilo = ref(0)
    const time = ref(0)

    const mountainId = ref(null)

    const isVisible = ref(true)
    const isDragOver = ref(false)

    const fileName = ref("")
    const fileContent = ref("")         // 存原始 XML
    const trackName = ref("")           // GPX track 名稱
    const trackPointsCount = ref(0)     // track points 數量
    const gpxCoords = ref([])  // 存 [lon, lat]
    const thought = ref("")
    const textCount = ref(0)

    // --- Props 1.定義父層傳入的山資料
    const props = defineProps({
    mountain: {
        type: Object,
        default: () => ({ name: "", kind: "" })
    }
    })

    // Emits
    const emit = defineEmits(["closeUploadModal", "saveGpx", "refreshStats"])

    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const MAX_TRACK_POINTS = 20000; // 最多2萬個軌跡點
    const ALLOWED_MIME_TYPES = ['application/xml', 'text/xml', 'application/gpx+xml'];

    const API_URL_1 = `${import.meta.env.VITE_AJAX_URL}/mychallenge_modal_1.php`
    const API_URL_2 = `${import.meta.env.VITE_AJAX_URL}/mychallenge_modal_2.php`

    function extractCoordinatesSafely(trkpts) {
        const coords = [];
        
        for (let i = 0; i < trkpts.length; i++) {
                const lat = parseFloat(trkpts[i].getAttribute("lat"));
                const lon = parseFloat(trkpts[i].getAttribute("lon"));
                
                // 驗證座標有效性
                if (isNaN(lat) || isNaN(lon) || 
                    lat < -90 || lat > 90 || 
                    lon < -180 || lon > 180) {
                    // console.warn(`跳過無效座標: ${lat}, ${lon}`);
                    continue;
                }
                
                coords.push([lon, lat]); // turf.js 預設是 [lon, lat]
            }
            
            return coords;
        }

        function calculateHeightSafely(trkpts) {
        let heightTotal = 0;
        let exElevation = parseFloat(trkpts[0].querySelector('ele')?.textContent || 0);
        
        // 驗證初始高度
        if (isNaN(exElevation) || exElevation < -500 || exElevation > 10000) {
            exElevation = 0;
        }
        
        trkpts.forEach((coordinatePoint, i) => {
            if (i === 0) return;
            
            const elevation = parseFloat(coordinatePoint.querySelector('ele')?.textContent || 0);
            
            // 驗證高度有效性
            if (isNaN(elevation) || elevation < -500 || elevation > 10000) {
                return;
            }
            
            if (elevation > exElevation) {
                const gain = elevation - exElevation;
                // 防止異常大的高度變化
                if (gain < 1000) {
                    heightTotal += gain;
                }
            }
            exElevation = elevation;
        });
        
        height.value = Number(heightTotal.toFixed(2));
    }

    function calculateDistanceSafely(trkpts) {
        function haversine(lat1, lon1, lat2, lon2) {
            const r = 6371e3; // 地球半徑(公尺)
            const rad = (v) => v * Math.PI / 180;

            const latGap = rad(lat2 - lat1);
            const lonGap = rad(lon2 - lon1);

            const a = Math.sin(latGap / 2) ** 2 +
                    Math.cos(rad(lat1)) * Math.cos(rad(lat2)) *
                    Math.sin(lonGap / 2) ** 2;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return r * c; // 公尺
        }

        let distance = 0;
        
        for (let i = 1; i < trkpts.length; i++) {
            const lat1 = parseFloat(trkpts[i-1].getAttribute("lat"));
            const lon1 = parseFloat(trkpts[i-1].getAttribute("lon"));
            const lat2 = parseFloat(trkpts[i].getAttribute("lat"));
            const lon2 = parseFloat(trkpts[i].getAttribute("lon"));
            
            // 驗證所有座標
            if ([lat1, lon1, lat2, lon2].some(coord => 
                isNaN(coord) || coord < -180 || coord > 180)) {
                continue;
            }
            
            const segmentDistance = haversine(lat1, lon1, lat2, lon2);
            
            // 防止異常大的距離（可能是錯誤資料）
            if (segmentDistance < 10000) { // 10km 以內才計算
                distance += segmentDistance;
            }
        }
        
        kilo.value = Number((distance / 1000).toFixed(2));
    }

    function calculateTimeSafely(trkpts) {
        const firstTime = trkpts[0].querySelector("time")?.textContent;
        const lastTime = trkpts[trkpts.length - 1].querySelector("time")?.textContent;
        
        if (!firstTime || !lastTime) {
            time.value = 0;
            return;
        }
        
        const startTime = new Date(firstTime);
        const endTime = new Date(lastTime);
        
        // 驗證時間有效性
        if (isNaN(startTime.getTime()) || isNaN(endTime.getTime())) {
            time.value = 0;
            return;
        }
        
        const speedMins = endTime - startTime;
        
        // 防止異常時間（負數或超過24小時）
        if (speedMins < 0 || speedMins > 24 * 60 * 60 * 1000) {
            time.value = 0;
            return;
        }
        
        const speedHrs = speedMins / 1000 / 60 / 60;
        time.value = Number(speedHrs.toFixed(2));
    }

    function isTrackWithinMountainArea(trkpts, mountainLat, mountainLon, radiusKm = 2) {
        // 計算兩點間距離的函數（Haversine公式）
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // 地球半徑(公里)
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // 檢查是否有任何軌跡點在山峰附近
        for (let i = 0; i < trkpts.length; i++) {
            const lat = parseFloat(trkpts[i].getAttribute("lat"));
            const lon = parseFloat(trkpts[i].getAttribute("lon"));
            
            // 驗證座標有效性
            if (isNaN(lat) || isNaN(lon)) continue;
            
            const distance = calculateDistance(mountainLat, mountainLon, lat, lon);
            
            if (distance <= radiusKm) {
                return true; // 找到在範圍內的點
            }
        }
        return false; // 沒有點在範圍內
    }

    // 驗證檔案格式
    function validateFile(file) {
        // 檢查副檔名
        const ext = file.name.split(".").pop().toLowerCase();
        if (ext !== "xml" && ext !== "gpx") {
            return false;
        }

        // 檔案大小檢查
        if (file.size > MAX_FILE_SIZE) {
            alert(`檔案過大！請使用小於 ${MAX_FILE_SIZE / 1024 / 1024}MB 的檔案`);
            return false;
        }
        
        // MIME 類型檢查
        if (file.type && !ALLOWED_MIME_TYPES.includes(file.type)) {
            alert('檔案類型不正確，請確認是有效的 GPX/XML 檔案');
            return false;
        }
        
        return true;
    }

    // 讀取並解析 XML
    function readFile(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const content = e.target.result;
            
            // 內容大小二次檢查
            if (content.length > MAX_FILE_SIZE) {
                alert('檔案內容過大');
                fileName.value = "";
                return;
            }
            
            fileContent.value = content;

            try {
                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(content, "text/xml");

                // 檢查 XML 解析錯誤
                const parseError = xmlDoc.querySelector('parsererror');
                if (parseError) {
                    alert("檔案格式錯誤，請確認是有效的 GPX 檔案");
                    fileName.value = "";
                    return;
                }

                // 檢查是否為 GPX 格式
                if (xmlDoc.documentElement.tagName !== 'gpx') {
                    alert("不是有效的 GPX 檔案格式");
                    fileName.value = "";
                    return;
                }

                // 地理位置驗證
                const trkpts = xmlDoc.querySelectorAll("trkpt");
                if (trkpts.length === 0) {
                    alert("GPX 檔案中沒有找到軌跡點！");
                    fileName.value = "";
                    return;
                }

                // 軌跡點數量限制
                if (trkpts.length > MAX_TRACK_POINTS) {
                    alert(`軌跡點過多（${trkpts.length}個），請使用少於 ${MAX_TRACK_POINTS} 個點的檔案`);
                    fileName.value = "";
                    return;
                }

                const isWithinArea = isTrackWithinMountainArea(
                    trkpts,
                    props.mountain.latitude,
                    props.mountain.longitude,
                    2 // 允許範圍：2公里
                );

                if (!isWithinArea) {
                    alert(`❌ 上傳失敗！\n\n此 GPX 軌跡沒有任何點在「${props.mountain.name}」2公里範圍內。\n請上傳正確山峰的軌跡記錄。`);
                    fileName.value = "";
                    return;
                }
                            
                // 安全地提取座標和計算數據
                gpxCoords.value = extractCoordinatesSafely(trkpts);
                calculateHeightSafely(trkpts);
                calculateDistanceSafely(trkpts);
                calculateTimeSafely(trkpts);

            } catch (err) {
                // console.error("XML parse error", err);
                alert("檔案解析失敗，請確認檔案格式正確");
                fileName.value = "";
            }
        };
        
        reader.onerror = () => {
            alert("檔案讀取失敗");
            fileName.value = "";
        };
        
        reader.readAsText(file);
    }

    // 文字輸入清理
    function sanitizeInput(text) {
        if (typeof text !== 'string') {
            return '';
        }
        
        return text.substring(0, 500);
    }
    

    // 拖曳事件
    
    function onDragOver() {
        isDragOver.value = true
    }

    function onDragLeave() {
        isDragOver.value = false
    }

    function onDrop(event) {
        isDragOver.value = false
        const file = event.dataTransfer.files[0]
        if (file && validateFile(file)) {
            fileName.value = file.name
            readFile(file)
        }else{
            alert("只允許上傳 XML/GPX 檔案！")
        }
    }

    // input change 事件
    function onFileChange(e) {
    const file = e.target.files[0]
        if (file && validateFile(file)) {
            fileName.value = file.name
            readFile(file)

        }else {
            alert("只允許上傳 XML/GPX 檔案！")
            e.target.value = "" // 清空 input
        }
    }
    
    function updateCount() {

        const cleanText = sanitizeInput(thought.value)
        if (cleanText !== thought.value) {
            thought.value = cleanText
        }

        textCount.value = thought.value.length
    }





    async function saveThought() {
        if (!props.mountain.name) {
            alert("沒有山的名稱，無法保存！");
            return;
        }

        // 檢查必要資料
        if (!fileName.value) {
            alert("請先上傳 GPX 檔案！");
            return;
        }

        try {
            // 準備要發送給 PHP 的資料
            const jsonData = {
                mountain_id: mountainId.value,
                height: height.value,
                distance: kilo.value,
                duration: time.value,
                content: thought.value,
                is_climbed: checkIfClimbed(),
            };

            function checkIfClimbed() {

                function calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371; // 地球半徑(公里)
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = 
                        Math.sin(dLat/2) * Math.sin(dLat/2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon/2) * Math.sin(dLon/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return R * c;
                }

                for (let coord of gpxCoords.value) {
                    const distance = calculateDistance(
                        props.mountain.latitude,
                        props.mountain.longitude,
                        coord[1],
                        coord[0]
                    );
                    if (distance < 0.05) return true;
                }
                return false;
            }
            
            // 發送 POST 請求到 PHP
            const response = await axios.post(
                API_URL_1,
                jsonData,
                {
                    withCredentials: true  // 讓 session 可以運作
                }
            );

            if (response.data.climbed) {
                alert(`恭喜！${props.mountain.name} 登頂成功，紀錄已儲存到資料庫！`);
            } else {
                alert(`${props.mountain.name} 的軌跡已記錄，繼續挑戰登頂吧！`);
            }

            // 發送資料給父層
            emit("saveGpx", {
                mountain: props.mountain.name,
                coords: gpxCoords.value
            });
            
            emit("refreshStats");

            // 清空表單
            fileName.value = "";
            thought.value = "";
            textCount.value = 0;
            height.value = 0;
            kilo.value = 0;
            time.value = 0;
            fileContent.value = "";
            gpxCoords.value = [];

            const fileInput = document.getElementById('theFile');
            if (fileInput) {
                fileInput.value = "";
            }

            emit("closeUploadModal", props.mountain.name);

        } catch (error) {
            // console.error('儲存失敗:', error);
            alert('儲存失敗，請稍後再試！');
        }
    }

    // 查詢山峰 ID 
    async function getMountainId(mountainName) {
        try {
        const response = await axios.get(`${ API_URL_2 }?name=${encodeURIComponent(mountainName)}`)
        
            if (response.data.success) {
                return response.data.mountain_id
            } else {
                // console.error('查詢失敗:', response.data.error)
                return null
            }
        } catch (error) {
            // console.error('查詢山峰 ID 失敗:', error)
            return null
        }
    }

    /* --- 載入已保存紀錄 --- */
    onMounted(async() => {
    if (props.mountain.name) {

        // 查詢山 ID
        mountainId.value = await getMountainId(props.mountain.name)

        // const key = `gpx-${props.mountain.name}`
        // const saved = localStorage.getItem(key)
        // if (saved) {
        //     const record = JSON.parse(saved)
        //     thought.value = record.thought || ""
        //     textCount.value = thought.value.length
        //     height.value = record.height || 0
        //     kilo.value = record.kilo || 0
        //     time.value = record.time || 0
        //     fileName.value = record.fileName || ""
        // }
    }
    })



</script>

<style scoped lang="scss">
    @import '@/assets/styles/main.scss';
    .modalOverlay{
        position: fixed;
        inset: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        background-color: rgba(20, 20, 20, 0.2);
        overflow-y: auto;
        padding: 60px 0;
        z-index: 214748;
    
        .mychallengeModal{
            position: relative;
            max-width: 800px;
            width: 100%;
            // max-height: 600px;
            padding: 60px;
            background-color: $ivory-gray-100;
            overflow: visible;
        
            .closeBtn{
                position: absolute;
                top: -12px;
                right: -12px;
                width: 40px;
                height: 40px;
                font-size: $pcFont-H4;
                color: #fff;
                background-color: $tag;
                border: none;
                border-radius: 50%;
                cursor: pointer;
            }
            
            .mountainInfo{
                display: flex;
                align-items: end;
                margin-bottom: 24px;
                
                h2{
                    font-size: $pcFont-H2;
                    font-weight: $semiBold;
                }

                h4{
                    font-size: $pcFont-H4;
                    font-weight: $semiBold;
                    margin-left: 20px;
                }
            }
        
            .uploadArea{
                position: relative;
                width: 100%;
                height: 300px;
                background-color: $bg-gray;
                border: 1px dashed $black-14;
                border-radius: 16px;
            
                .add{
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
            
                    font-size: $pcFont-H1-l;
                    font-weight: $medium;
                    line-height: $lineHeight-title-120;
                    color: $black-14;

                    @media screen and (max-width: 750px) {
                        font-size: $pcFont-H3;
                    }
                }
                
                #theFile{
                    width: 100%;
                    height: 100%;
                    
                    position: absolute;
                    top: 0;
                    left: 0;
                    
                    opacity: 0;
                    cursor: pointer;
                }
            }
            .score{
                display: flex;
                margin-top: 24px;
            
                div{
                    width: calc(100% / 3);
                
                }
                p{
                    font-size: $pcFont-p-s;
                    font-weight: $bold;
                    line-height: $lineHeight-p-150;
                    display: block;
                    
                    span{
                        font-size: $pcFont-H1-m;
                        font-weight: $medium;
                        line-height: $lineHeight-title-120;

                        @media screen and (max-width: 750px) {
                            font-size: $pcFont-H4;
                        }
                    }
                }
            }
            
            .think{
                margin: 56px 0;
                position: relative;
                
                h4{
                    font-size: $pcFont-H4;
                    font-weight: $semiBold;
                    margin-bottom: 24px;
                }
                
                
                textarea{
                    width: 100%;
                    height: 300px;
                    padding: 20px;
                    font-size: 20px;
                    background-color: $bg-gray;
                    border: 1px solid $black-14;
                    border-radius: 16px;
                    box-sizing: border-box;
                    resize: none;
                }

                .textCount{
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    padding-right: 12px;
                    padding-bottom: 12px;
                }
            }
            
            .buttunWrapper{
                display: flex;
                justify-content: center;
            
                button{
                    height: 46px;
                    width: 120px;
                    align-items: center;
                    padding: 8px 12px;
                    font-size: $pcFont-p-m;
                    font-weight: $semiBold;
                    color: #fff;
                    background-color: $tag;
                    border: 1px solid $tag;
                    border-radius: 999px;
                    cursor: pointer;
                }
            }

            @media screen and (max-width: 1200px) {
                position: relative;
            }

            @media screen and (max-width: 1000px) {
                max-width: 800px;
                width: 80%;
                padding: 20px;
            }

            @media screen and (max-width: 430px) {
                max-width: 800px;
                width: 80%;
            }
        }

        @media screen and (max-width: 1200px) {
            z-index: 20 !important;
            padding: 20px 0;
        }
    }
    
</style>