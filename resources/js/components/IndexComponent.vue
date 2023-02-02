<template>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <headerComponent />

                    <div class="card-body">

                        <div class="row">
                            <div class="col-lg-9 col-sm-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="textSearch" v-model="textSearch" @keyup="searchKeyFunction()">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="d-flex flex-row">
                                    <div class="w-50">
                                        <button type="submit" class="btn btn-primary mb-2 w-100"
                                            @click="searchFunction()">ค้นหา</button>
                                    </div>
                                    <div class="w-50 ml-1">
                                        <button type="submit" class="btn btn-warning mb-2 w-100"
                                            @click="searchClearFunction()">ล้าง</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="showMap == true" class="row m-0 w-100">
                            <GmapMap :center="center" :zoom="14" map-style-id="roadmap" :options="mapOptions"
                                style="width: 100%; height: 700px" ref="mapRef">
                                <GmapMarker :key="index" v-for="(m, index) in markers" :position="m" :clickable="true"
                                    @click="[center = m, viewShowDataDetailFunction(m.place_id)]" />
                            </GmapMap>
                        </div>

                        <h3>รายการร้านที่พบ : {{ textSearch }}</h3>
                        <div class="row p-2">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">ภาพประกอบ</th>
                                        <th scope="col">ชื่อร้าน</th>
                                        <th scope="col">คำแนน</th>
                                        <th scope="col">รายละเอียด</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(value, i) in dataList" :key="i" :class="{ showColor: value.isActive }">
                                        <th scope="row">{{ i + 1 }}</th>
                                        <td><img :src="value.icon" class="w-img"></td>
                                        <td>{{ value.name }}</td>
                                        <td>
                                            <div class="d-flex flex-row">
                                                <div v-for="(n, index) in parseInt(value.rating)" :key="index">
                                                    <div>
                                                        <svg style="width:15px" fill="#FBBC04"
                                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                            <path
                                                                d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <svg @click="viewDetailFunction(value.place_id)" class="w-img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path
                                                    d="M11.5 16C12.37 16 13.19 15.74 13.88 15.3L16.32 17.74L17.74 16.32L15.3 13.89C15.74 13.19 16 12.38 16 11.5C16 9 14 7 11.5 7S7 9 7 11.5 9 16 11.5 16M11.5 9C12.88 9 14 10.12 14 11.5S12.88 14 11.5 14 9 12.88 9 11.5 10.12 9 11.5 9M20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4M20 18H4V6H20V18Z" />
                                            </svg>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <b-modal id="detailModal" size="lg" centered hide-header hide-footer hide-header-close>
            <div class="modal-header">
                <div class="row">
                    <h4>รายละเอียดร้านค้า: {{ deatilRes.name }}</h4>
                    <hr>
                </div>
            </div>
            <div class="modal-body" v-if="deatilRes">
                <div class="row mt-2" v-if="deatilRes.photosImage">
                    <div class="col-6" v-for="(value, i) in deatilRes.photosImage" :key="i">
                        <img class="img-thumbnail" :src="value">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="d-flex flex-row" v-if="deatilRes.rating">
                        <div>คะแนน :</div>
                        <div v-for="(n, index) in parseInt(deatilRes.rating)" :key="index">
                            <div>
                                <svg style="width:15px" fill="#FBBC04" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <p>วันเวลาเปิด - ปิด : </p>
                    <div v-if="deatilRes.opening_hours">
                        <p class="pl-2" v-for="(value, i) in deatilRes.opening_hours.weekday_text" :key="i">{{ value }}
                        </p>
                    </div>
                </div>

                <div class="row mt-2">
                    <p>ที่อยู่ : </p>
                    <font class="pl-2" v-html="deatilRes.adr_address"></font>
                </div>

                <div class="row mt-4">
                    <div class="col-6">
                        <a :href="deatilRes.url" target="_blank" class="btn btn-success w-100">ดูแผนที่</a>
                    </div>
                    <div class="col-6">
                        <a :href="deatilRes.website" target="_blank" class="btn btn-primary w-100">ดูเว็บหลัก</a>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
            </div>
        </b-modal>


    </div>
</template>

<script>
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import swal from "sweetalert2";
import loading from 'vuejs-loading-screen'
import * as VueGoogleMaps from 'vue2-google-maps'

import headerComponent from './headerComponent.vue';

Vue.use(loading, {
    bg: '#808080ad',
    slot: `
    <div class="px-5 py-3 bg-gray-800 rounded">
      <h3 class="text-3xl text-white"><i class="fas fa-spinner fa-spin"></i> Loading...</h3>
    </div>
  `
})

Vue.use(VueGoogleMaps, {
    load: {
        key: "",
    }
});

Vue.use(BootstrapVue)
export default {
    props: {
        key_data: null,
    },
    components: {
        headerComponent: headerComponent,
    },
    data() {
        return {
            key: this.key_data,

            textSearch: "บางซื่อ",
            type: "restaurant",

            showMap: null,

            dataList: [],
            deatilRes: {},
            deatilResPhotos: {},

            fixTextSearch: "บางซื่อ",

            markers: [],
            // marker: { position: { lat: 10, lng: 10 } },
            center: { lat: 13.82387082989272, lng: 100.5309840798927 },
            mapOptions: {
                disableDefaultUI: true,
            },
        }
    },
    mounted() {
        // this.setMapFunction();
        this.getLocationList();
    },
    methods: {
        searchFunction() {
            var oo = this;
            var textSearch = this.textSearch;

            if (textSearch) {
                this.getLocationList();

            } else {
                swal
                    .fire("ดำเนินการไม่สำเร็จ!", "กรุณากรอกข้อมูล", "error")
                    .then(function (response) {
                    });
            }
        },

        searchClearFunction() {
            this.textSearch = this.fixTextSearch;

            this.searchFunction();
        },

        searchKeyFunction() {
            this.dataList = [];
        },

        async getLocationList() {
            this.$isLoading(true)

            var oo = this;
            await axios
                .get("/getLocationList", {
                    params: {
                        textSearch: this.textSearch,
                        type: this.type,
                    }
                })
                .then(function (response) {
                    var detail = response.data.detail;
                    const newArr2 = detail.map(v => Object.assign(v, { isActive: false }))

                    oo.dataList = newArr2
                    // oo.dataList = response.data.detail
                    oo.setMapFunction(response.data.detail);
                })
                .catch((error) => { });

            this.$isLoading(false)

        },

        setMapFunction(data) {
            var mark = [];
            var center = {};
            data.forEach((element, index) => {
                mark.push({ lat: element.geometry.location.lat, lng: element.geometry.location.lng, place_id: element.place_id })

                center = { lat: element.geometry.location.lat, lng: element.geometry.location.lng };
            });

            // this.markers.push({ position: mark });
            this.markers = mark
            this.center = center

            this.showMap = true;
        },

        detailModalFunction() {

        },

        async viewDetailFunction(place_id) {
            this.$isLoading(true)
            var oo = this;

            oo.deatilRes = {};
            await axios
                .get("/getDetailData", {
                    params: {
                        place_id: place_id,
                        textSearch: this.textSearch,
                        type: this.type,
                    }
                })
                .then(function (response) {
                    oo.deatilRes = response.data.detail;
                    oo.$bvModal.show('detailModal');
                })
                .catch((error) => { });
            this.$isLoading(false)
        },

        viewShowDataDetailFunction(place_id) {
            var dataList = this.dataList;

            this.dataList.map(function (x) {
                x.isActive = false;
                return x
            });

            const findIndex = dataList.findIndex(element => element.place_id == place_id);

            this.dataList[findIndex].isActive = true;
        },

    },

}
</script>

<style scoped>
h1 {
    display: inline-block;
    font-weight: 100;
    font-size: pxtoem(45, 16);
    border-bottom: 1px solid grey !important;
    margin: 0 0 0.1em 0;
    padding: 0 0 0.4em 0;
}

h3 {
    font-size: pxtoem(20, 16);
    margin: 1em 0 0.4em 0;
}

.header {
    padding: 1.5em 2.5em;
    border-bottom: 1px solid grey;
    background: url(https://images2.imgbox.com/a5/2e/m3lRbCCA_o.jpg) left -80px;
    color: white;
}

.w-img {
    width: 30px;
    height: 30px;
}

.showColor {
    background-color: #c7c7c7;
}
</style>
