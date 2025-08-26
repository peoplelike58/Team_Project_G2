<template>
    <div class="modalOverlay" @click.self="$emit('closeUploadModal')">
        <section class="mychallengeModal">
            <button class="closeBtn" @click="$emit('closeUploadModal')">x</button>
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
                <textarea id=""
                    v-model="thought"
                    maxlength="200"
                    @input="updateCount"
                ></textarea>
                <p class="textCount">{{ textCount }} / 200</p>
            </article>
            <div class="buttunWrapper">
                <button @click="saveThought">提交</button>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRecordStore } from "@/stores/recordStore"

    let height = ref(0)
    let kilo = ref(0)
    let time = ref(0)

    // let mountain = ref({name: '玉山', kind: '大百岳'})

    const isVisible = ref(true)

    // --- 1.定義父層傳入的山資料
    const props = defineProps({
    mountain: {
        type: Object,
        default: () => ({ name: "", kind: "" })
    }
    })

    const isDragOver = ref(false)
    const fileName = ref("")
    
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
            console.log("拖曳上傳：", file)
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
            console.log("點擊選擇：", file)
            readFile(file)

        }else {
            alert("只允許上傳 XML/GPX 檔案！")
            e.target.value = "" // 清空 input
        }
    }


    const fileContent = ref("")         // 存原始 XML
    const trackName = ref("")           // GPX track 名稱
    const trackPointsCount = ref(0)     // track points 數量

    // 驗證檔案格式
    function validateFile(file) {
    const ext = file.name.split(".").pop().toLowerCase()
        return ext === "xml" || ext === "gpx"
    }

    const gpxCoords = ref([])  // 存 [lon, lat]
    // 讀取並解析 XML
    function readFile(file) {
    const reader = new FileReader()
    reader.onload = (e) => {
        fileContent.value = e.target.result

        try {
            const parser = new DOMParser()
            const xmlDoc = parser.parseFromString(fileContent.value, "text/xml")

            // GPX <name>
            const nameTag = xmlDoc.querySelector("gpx > metadata > name, trk > name")
            trackName.value = nameTag ? nameTag.textContent : "未找到名稱"

                // 🔹 檢查 GPX 名稱是否含有當前 modal 山名
            if (!trackName.value.includes(props.mountain.name)) {
                alert(`GPX 檔案名稱與「${props.mountain.name}」不符，請上傳正確的紀錄！`)
                fileName.value = ""
                return
            }
                
            // 取得 <trkpt> gpx檔裡面的標籤，把每一個座標點的數據包住
            const trkpts = xmlDoc.querySelectorAll("trkpt")
            gpxCoords.value = Array.from(trkpts).map(pt => {
                const lat = parseFloat(pt.getAttribute("lat"))
                const lon = parseFloat(pt.getAttribute("lon"))
                return [lon, lat]   // turf.js 預設是 [lon, lat]
            })
            
            // 計算gpx高度
            // 變數名稱:elevation海拔, exElevation前一個高度, coordinatePoint座標點
            let heightTotal = 0
            let exElevation = parseFloat(trkpts[0].querySelector('ele')?.textContent || 0)

            trkpts.forEach((coordinatePoint, i) => {
                
                if (i === 0) return

                const elevation = parseFloat(coordinatePoint.querySelector('ele')?.textContent || 0)
                if(elevation > exElevation){
                    heightTotal += elevation - exElevation

                }
                exElevation = elevation
            })
            height.value = Number(heightTotal.toFixed(2))

            // 計算gpx里程
            // 變數名稱:haversine半正矢公式(用經緯度計算兩點的距離),lat緯度, lon經度, r半徑, v角度, rad弧度, lonGap經度差, latGap緯度差, a點與點的距離, c角距離, distance距離
            function haversine(lat1, lon1, lat2, lon2){
                const r = 6371e3 // 地球半徑(公尺)
                const rad = ( v ) => v * Math.PI / 180

                const latGap = rad(lat2 - lat1)
                const lonGap = rad(lon2 - lon1)

                const a = Math.sin(latGap / 2) ** 2 +
                        Math.cos(rad(lat1)) * Math.cos(rad(lat2)) *
                        Math.sin(lonGap / 2) ** 2
                const c = 2  * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))
                return r * c //公尺
            }
            let distance = 0
            for (let i = 1; i < trkpts.length; i++) {
                const lat1 = parseFloat(trkpts[i-1].getAttribute("lat"));
                const lon1 = parseFloat(trkpts[i-1].getAttribute("lon"));
                const lat2 = parseFloat(trkpts[i].getAttribute("lat"));
                const lon2 = parseFloat(trkpts[i].getAttribute("lon"));

                distance += haversine(lat1, lon1, lat2, lon2);
            }
            kilo.value = Number((distance / 1000).toFixed(2))    // 換算成 km

            // 計算 gpx 時間
            const startTime = new Date(trkpts[0].querySelector("time")?.textContent);
            const endTime   = new Date(trkpts[trkpts.length - 1].querySelector("time")?.textContent);

            const speedMins = endTime - startTime;              // 毫秒
            const speedHrs = speedMins / 1000 / 60 / 60;      // 小時
            time.value = Number(speedHrs.toFixed(2))

        } catch (err) {
            console.error("XML parse error", err)
            trackName.value = "解析失敗"
        }
    }
        reader.readAsText(file)
    }

    const thought = ref("")
    const textCount = ref(0)

    function updateCount() {
        textCount.value = thought.value.length
    }

    const emit = defineEmits(["closeUploadModal","saveGpx"])

    function saveThought() {
        if (!props.mountain.name) {
            alert("沒有山的名稱，無法保存！")
            return
        }

        const key = `gpx-${props.mountain.name}`

        // 建立一個完整紀錄物件 gpxPoint 
        const record = {
            thought: thought.value,
            height: height.value,
            kilo: kilo.value,
            time: time.value,
            fileName: fileName.value
        }

        // 存 localStorage
        localStorage.setItem(key, JSON.stringify(record))

        // 存 Pinia
        const recordStore = useRecordStore()
        recordStore.saveRecord(props.mountain.name, record)

        alert(`對於 ${props.mountain.name} 的紀錄已保存！`)

        // 發送資料給父層
        emit("saveGpx", { mountain: props.mountain.name, coords: gpxCoords.value })
        emit("closeUploadModal")
    }

    /* --- 載入已保存紀錄 --- */
    onMounted(() => {
    if (props.mountain.name) {
        const key = `gpx-${props.mountain.name}`
        const saved = localStorage.getItem(key)
        if (saved) {
            const record = JSON.parse(saved)
            thought.value = record.thought || ""
            textCount.value = thought.value.length
            height.value = record.height || 0
            kilo.value = record.kilo || 0
            time.value = record.time || 0
            fileName.value = record.fileName || ""
        }
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
        }
    }



    @media screen and (max-width: 430px) {
		.modalOverlay{
            z-index: 20 !important;
            padding: 20px 0;
    
            .mychallengeModal{
                position: relative;
                max-width: 800px;
                width: 80%;
                padding: 20px;

                .uploadArea{
                
                    .add{
                        font-size: $pcFont-H3;
                    }
                    
                }
                .score{
                
                    p{
                        span{
                            font-size: $pcFont-H4;
                        }
                    }
                }
            }
        }
    }
</style>