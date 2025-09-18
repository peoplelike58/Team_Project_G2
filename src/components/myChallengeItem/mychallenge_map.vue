<template>
    <section class="map" id="map">
		<div class="remind">
			<p>點擊 <img :src="`${BASE}images/myChallenge/mountain.png`" alt="山icon">即可上傳您的足跡</p>
		</div>
        <l-map
        :zoom="zoom" 
        :center="center"
        @ready="onMapReady"
        >
            <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" 
                attribution="&copy; OpenStreetMap contributors" />
              <!-- <l-marker 
              v-for="mountain in mountains" 
              @click="emit('openUploadModal', mountain.name)" 
              :lat-lng="[mountain.latitude, mountain.longitude]"
              :icon="getIcon(mountain.icon)" 
                  /> -->
        </l-map>

    </section>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'

import 'leaflet/dist/leaflet.css'
import { Icon } from 'leaflet'
import { LMap, LTileLayer } from '@vue-leaflet/vue-leaflet'
import L from "leaflet"
import "leaflet.markercluster"

const BASE = import.meta.env.BASE_URL
const jsonPath = `${BASE}json/mychallenge/ranks.json`

function getIcon(fileName) {
	return new Icon({
		iconUrl: `${BASE}images/myChallenge/${fileName}`,
		iconRetinaUrl: `${BASE}images/myChallenge/${fileName}`,
		shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
		iconSize: [41, 41],
		iconAnchor: [16, 32],
		popupAnchor: [0, -32],
		shadowSize: [41, 41],
		shadowAnchor: [12, 41]
	})
}
 
const centerLatitude = 23.973786
const centerLongitude = 120.9817558

const zoom = ref(8)
const center = ref([centerLatitude, centerLongitude])

const props = defineProps({
	mountains: {
		type: Array,
		default: () => [],
	},
	isLoggedIn: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(["openUploadModal"])

// 存所有 marker 的 Map
const markerMap = new Map()
let clusterGroup = null
let mapInstance = null

function handleMarkerClick(mountainName) {
	console.log('點擊山峰:', mountainName) // 測試用 log
	console.log('登入狀態:', props.isLoggedIn) // 測試用 log

	emit('openUploadModal', mountainName)
}

// --- 建立地圖和icon ---
function onMapReady(map) {
	mapInstance = map
	// 1. 建立「群組標記」功能 - 當地圖縮小時，附近的山會合併成一個圓圈
	clusterGroup = L.markerClusterGroup({
	showCoverageOnHover: false,				// 滑鼠懸停時不顯示覆蓋範圍
	iconCreateFunction: (cluster) => {		// 群組的設定
			const count = cluster.getChildCount()	// 群組裡有幾座山

			const size = Math.min(40 + count, 100)	// 圓圈大小：最小40px，最大100px

			// 取得群組裡的所有子 marker
			const children = cluster.getAllChildMarkers()

			// 檢查群組內是否有「已攀登」的山
			const hasFlag = children.some(mountain => mountain.isClimbed === true)
			// 如果有任何一座山被攀登過，群組就顯示旗子圖示，否則顯示山峰圖示
			const iconFile = hasFlag ? "flag.png" : "mountain.png"
			// 建立群組的視覺外觀（圓形背景 + 圖示）
			return L.divIcon({
				html: `
					<div style="
						width:${size}px;
						height:${size}px;
						border-radius:50%;
						border: 3px solid #ff6b6b;
						display: flex;
						align-items: center;
						justify-content: center;"
						>
					<img 
						src="${BASE}images/myChallenge/${iconFile}" 
						style="width:${size * 0.6}px; height:${size * 0.6}px;" 
					/>
					</div> `,
				className: 'myCluster',
				iconSize: [size, size]
			})
		}
	})

	// 2. 把父層傳進來的山 (props.mountains) 一一加到 群組
	props.mountains.forEach(mountain => {
		const marker = L.marker([mountain.latitude, mountain.longitude], {
			icon: getIcon(mountain.icon)	// 單一 marker 的 icon (小旗子 / 山 icon)
		})
		marker.isClimbed = (mountain.icon === "flag.png") 
		marker.on("click", () => handleMarkerClick(mountain.name))
		clusterGroup.addLayer(marker)		// 加入群組

		// 加入 tooltip 顯示山名
		marker.bindTooltip(mountain.name, {
			permanent: true,        // 永久顯示，不需要滑鼠懸停
			direction: 'bottom',    // 顯示在下方
			offset: [5, 5],        // 往下偏移 10px
			className: 'mountain-name-tooltip'  // 自定義 CSS 類別
		})
		
		clusterGroup.addLayer(marker)
		markerMap.set(mountain.name, marker)  // ✅ 存入 Map
	})

	// 3. 最後把 群組丟到地圖
	map.addLayer(clusterGroup)
}

// 更新攀登狀態
function setClimbed(mountainName) {
	// 當用戶成功上傳GPX後，這個函數被呼叫

	// 1. 更新資料源
	const mountain = props.mountains.find(mountain => mountain.name === mountainName)
	if (mountain) {
		mountain.icon = "flag.png"
	}

	// 2. 更新對應的 marker
	const marker = markerMap.get(mountainName)
	if (marker) {
		marker.isClimbed = true			// 標記為已攀登
		marker.setIcon(getIcon("flag.png"))		// 改成旗子圖示
		
// 3. 強制重建群組（這是為了更新群組圖示）
		if (clusterGroup && mapInstance) {
			// 完全移除舊的 cluster
			mapInstance.removeLayer(clusterGroup)
			
			// 建立全新的 cluster 群組
			clusterGroup = L.markerClusterGroup({
				showCoverageOnHover: false,
				iconCreateFunction: (cluster) => {
					const count = cluster.getChildCount()
					const size = Math.min(40 + count, 100)
					const children = cluster.getAllChildMarkers()
					const hasFlag = children.some(m => m.isClimbed === true)
					const iconFile = hasFlag ? "flag.png" : "mountain.png"
					

					return L.divIcon({
						html: `
							<div style="
								width:${size}px;
								height:${size}px;
								border-radius:50%;
								border: 3px solid #ff6b6b;
								display: flex;
								align-items: center;
								justify-content: center;"
								>
							<img 
								src="${BASE}images/myChallenge/${iconFile}" 
								style="width:${size * 0.6}px; height:${size * 0.6}px;" 
							/>
							</div> `,
						className: 'myCluster',
						iconSize: [size, size]
					})
				}
			})
			
			// 重新加入所有 markers
			props.mountains.forEach(mount => {
				const mountainsMarker = markerMap.get(mount.name)
				if (mountainsMarker) {
					mountainsMarker.isClimbed = (mount.icon === "flag.png")
					mountainsMarker.setIcon(getIcon(mount.icon))

					// 重新綁定點擊事件（包含登入檢查）
					mountainsMarker.off('click') // 先移除舊的事件監聽
					mountainsMarker.on('click', () => handleMarkerClick(mount.name))

					clusterGroup.addLayer(mountainsMarker)
				}
			})
			
			// 加回地圖
			mapInstance.addLayer(clusterGroup)
		}
	}
}
defineExpose({ setClimbed })

const mapReady = ref(false)

onMounted(() => {
	setTimeout(() => {
		mapReady.value = true
	}, 300)
})

watch(() => props.mountains, (newMountains) => {
	if (newMountains && newMountains.length > 0 && mapInstance && clusterGroup) {
		// 重新初始化地圖標記
		nextTick(() => {
			// 清空現有標記
			clusterGroup.clearLayers()
			markerMap.clear()
			
			// 重新加入標記
			newMountains.forEach(mountain => {
				const marker = L.marker([mountain.latitude, mountain.longitude], {
					icon: getIcon(mountain.icon)
				})
				marker.isClimbed = (mountain.icon === "flag.png") 
				
				// 檢查登入
				marker.on("click", () => handleMarkerClick(mountain.name))

				marker.bindTooltip(mountain.name, {
					permanent: true,
					direction: 'bottom',
					offset: [5, 5], 
					className: 'mountain-name-tooltip'
				})

				clusterGroup.addLayer(marker)
				markerMap.set(mountain.name, marker)
			})
		})
	}
}, { immediate: true, deep: true })

</script>

<style scoped lang="scss">
    @import '../../assets/styles/main.scss';

    .map{
		position: relative;
		max-width: 564px;
        max-height: 780px;
        width: 100%;
        height: 100%;
        border: 1px solid black;
		z-index: 1 !important;
        
		.remind{
			position: absolute;
			top: 20px;
			right: 20px;
			font-size: $pcFont-p-s;
			font-weight: $bold;
			padding: 4px 16px;
			background-color: white;
			border-radius: 16px;
			z-index: 2222 !important;

			img{
				width: 16px;
				height: 16px;
			}
		}

		@media screen and (max-width: 1200px) {
				width: 100%;
				height: 100%;
				z-index: 1 !important;
		}

		@media screen and (max-width: 768px) {
				max-width: 768px;
				max-height: 780px;
		}
    }

    .myCluster{
        border-radius: 50% ;   /* 圓形 */
        border: 3px solid #ff6b6b; /* 紅色邊框 */
        background: white ;    /* 白底 */
        color: #333;
        font-weight: bold;
        font-size: 14px;
        line-height: 40px;               /* 垂直置中 */
        text-align: center;
        width: 40px;
        height: 40px;
        margin-left: -20px;              /* 負一半，讓圓心對準座標 */
        margin-top: -20px;
        box-shadow: 0 0 5px rgba(0,0,0,0.3);

		&:focus,
		&:focus-visible,
		&:hover {
			outline: none;
			box-shadow: none;
		}
    }

</style>