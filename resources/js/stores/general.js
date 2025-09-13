import { defineStore } from 'pinia'

export const useGeneralStore = defineStore('general', {
    state: () => ({
        smallSideBar: false,
        paginationNumber: 10,
    }), 
    persist: true, 
})
