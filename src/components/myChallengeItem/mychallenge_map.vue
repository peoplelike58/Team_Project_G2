<template>
    <section class="map" id="map">
        <l-map :zoom="zoom" :center="center">
            <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" 
                attribution="&copy; OpenStreetMap contributors">
            </l-tile-layer>
            <l-marker 
            v-for="mountain in mountains" 
            @click="emit('openUploadModal', mountain.name)" 
            :lat-lng="[mountain.latitude, mountain.longitude]"
            :icon="getIcon(mountain.icon)" 
                >
            </l-marker>
        </l-map>

    </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
// import * as turf from '@turf/turf'

import 'leaflet/dist/leaflet.css'
import { Icon } from 'leaflet'
import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet'

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
    required: true
  }
})

const emit = defineEmits(["openUploadModal"])

// function checkAndMarkMountains(gpxPoints) {
//   mountains.value.forEach((mountain, index) => {
//     const mountainPoint = turf.point([mountain.longitude, mountain.latitude])

//     let climbed = false
//     for (const [lon, lat] of gpxPoints) {
//       const gpxPoint = turf.point([lon, lat])
//       const distance = turf.distance(mountainPoint, gpxPoint, { units: 'kilometers' })

//       if (distance < 0.2) { // 200 公尺內就算登頂
//         climbed = true
//         break
//       }
//     }

//     if (climbed) {
//       mountains.value[index].icon = "flag.png"
//     }
//   })
// }

</script>

<style scoped lang="scss">
    @import '../../assets/styles/main.scss';

    .map{
        max-width: 564px;
        max-height: 780px;
        width: 100%;
        height: 780px;
        border: 1px solid black;
    }

</style>