<template>
    <section class="map" id="map">
        <l-map :zoom="zoom" :center="center">
            <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" attribution="&copy; OpenStreetMap contributors">
            </l-tile-layer>
            <l-marker :lat-lng="markerLatLng">
            </l-marker>
        </l-map>

    </section>
</template>

<script setup>
import { ref } from 'vue'

import 'leaflet/dist/leaflet.css'
import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet'

const latitude = 23.973786
const longitude = 120.9817558

const zoom = ref(8)
const center = ref([latitude, longitude])
const markerLatLng = ref([latitude, longitude])

</script>

<style scoped lang="scss">
    @import '../../assets/styles/main.scss';

    .map{
        max-width: 564px;
        max-height: 780px;
        width: 100%;
        height: 100%;
        border: 1px solid black;
    }

</style>