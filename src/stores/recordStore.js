import { defineStore } from "pinia"
import { computed, ref } from "vue"

export const useRecordStore = defineStore("recordStore", () => {
    // 每座山的紀錄，key = 山名
    const mountainRecords = ref({})

    // 新增或覆蓋某座山的紀錄
    function saveRecord(mountain, data) {
        mountainRecords.value[mountain] = {
        height: data.height || 0,
        kilo: data.kilo || 0,
        time: data.time || 0,
        thought: data.thought || "",
        fileName: data.fileName || ""
        }
        // 同步存 localStorage（可選）
        localStorage.setItem(`gpx-${mountain}`, JSON.stringify(mountainRecords.value[mountain]))
    }

    // 從 localStorage 載入既有資料
    function loadRecord(mountain) {
        const saved = localStorage.getItem(`gpx-${mountain}`)
        if (saved) {
        mountainRecords.value[mountain] = JSON.parse(saved)
        }
    }

    // 把 localStorage 的紀錄帶進 Pinia
    function loadAllRecords() {
        for (let key in localStorage) {
            if (key.startsWith("gpx-")) {
            const mountain = key.replace("gpx-", "")
            mountainRecords.value[mountain] = JSON.parse(localStorage.getItem(key))
            }
        }
        }

    // 總和
    const heightTotal = computed(() =>
        Object.values(mountainRecords.value).reduce((sum, r) => sum + (r.height || 0), 0)
    )
    const kiloTotal = computed(() =>
        Object.values(mountainRecords.value).reduce((sum, r) => sum + (r.kilo || 0), 0)
    )
    const timeTotal = computed(() =>
        Object.values(mountainRecords.value).reduce((sum, r) => sum + (r.time || 0), 0)
    )

    return {
        mountainRecords,
        saveRecord,
        loadRecord,
        loadAllRecords,
        heightTotal,
        kiloTotal,
        timeTotal }
})