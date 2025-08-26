<template>
    <section class="map" id="map">
		<div class="remind">
			<p>點擊 <img src="/images/myChallenge/mountain.png" alt="山icon">即可上傳您的足跡</p>
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
import { ref, onMounted } from 'vue'
// import * as turf from '@turf/turf'

import 'leaflet/dist/leaflet.css'
import { Icon } from 'leaflet'
import { LMap, LTileLayer } from '@vue-leaflet/vue-leaflet'
import L from "leaflet"
import "leaflet.markercluster"

function getIcon(fileName) {
	return new Icon({
		iconUrl: `/images/myChallenge/${fileName}`,
		iconRetinaUrl: `/images/myChallenge/${fileName}`,
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
		// default: () => [],
		// required: true
	}
})

const emit = defineEmits(["openUploadModal"])

// 存所有 marker 的 Map
const markerMap = new Map()
let clusterGroup = null
let mapInstance = null

function onMapReady(map) {
	mapInstance = map
	// 1. 建立 cluster 群組，並自訂 cluster 的 icon 外觀
	clusterGroup = L.markerClusterGroup({
	showCoverageOnHover: false,
	iconCreateFunction: (cluster) => {
			const count = cluster.getChildCount()
			// 基於數量計算大小（最小 40px，最大 100px）
			const size = Math.min(40 + count, 100)	// icon 大小依 count 動態改變

			// 取得群組裡的所有子 marker
			const children = cluster.getAllChildMarkers()

			// 判斷 cluster 裡有沒有旗子
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
						src="/images/myChallenge/${iconFile}" 
						style="width:${size * 0.6}px; height:${size * 0.6}px;" 
					/>
					</div> `,
				className: 'myCluster',
				iconSize: [size, size]
			})
		}
	})

	// 2. 把父層傳進來的山 (props.mountains) 一一加到 cluster
	props.mountains.forEach(m => {
		const marker = L.marker([m.latitude, m.longitude], {
			icon: getIcon(m.icon)	// 單一 marker 的 icon (小旗子 / 山 icon)
		})
		marker.isClimbed = (m.icon === "flag.png") // 初始狀態
		marker.on("click", () => emit("openUploadModal", m.name))	// 點擊事件
		clusterGroup.addLayer(marker)

		markerMap.set(m.name, marker)  // ✅ 存入 Map
	})

	// 3. 最後把 cluster 群組丟到地圖
	map.addLayer(clusterGroup)
}

// ✅ 外部呼叫：把某座山改成旗子
function setClimbed(mountainName) {
	// 1. 更新資料源
	const mountain = props.mountains.find(m => m.name === mountainName)
	if (mountain) {
		mountain.icon = "flag.png"
	}

	// 2. 更新對應的 marker
	const marker = markerMap.get(mountainName)
	if (marker) {
		marker.isClimbed = true
		marker.setIcon(getIcon("flag.png"))
		
// 3. 強制重建 cluster - 先移除再重新加入
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
								src="/images/myChallenge/${iconFile}" 
								style="width:${size * 0.6}px; height:${size * 0.6}px;" 
							/>
							</div> `,
						className: 'myCluster',
						iconSize: [size, size]
					})
				}
			})
			
			// 重新加入所有 markers
			props.mountains.forEach(m => {
				const mk = markerMap.get(m.name)
				if (mk) {
					mk.isClimbed = (m.icon === "flag.png")
					mk.setIcon(getIcon(m.icon))
					clusterGroup.addLayer(mk)
				}
			})
			
			// 加回地圖
			mapInstance.addLayer(clusterGroup)
		}
	}
}
defineExpose({ setClimbed })



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

    }
	
	.myCluster,
	.myCluster:focus,
	.myCluster:focus-visible,
	.myCluster:hover {
		outline: none;
		box-shadow: none;
	}

	@media screen and (max-width: 1200px) {
		.map{
            width: 100%;
            height: 100%;
			z-index: 1 !important;
        }
	}

	@media screen and (max-width: 430px) {
		.wrapper{
			// max-width: 100%;
            width: 100%;
            height: 100%;
        }
	}
</style>