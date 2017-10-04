<template>
    <div class="page ticket-metrics">
        <div class="page-header">
            <h2>OIT Ticket Metrics</h2>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label>Resolution (Days)</label>
                <select class="m-r-2" v-model="resolution">
                    <option value="1">1</option>
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="60">60</option>
                    <option value="90">90</option>
                </select>

                <label >History (Years)</label>
                <select class="m-r-2" v-model="history">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
                <button @click="fetch" class="btn btn-sm btn-primary" :class="{disabled : busy}" :disabled="busy">Go</button>
            </div>
            <div class="col-md-12 m-t-2 relative">
                <div v-if="busy" class="overlay">
                    <i class="fa fa-fw fa-refresh fa-spin"></i>
                </div>
                <canvas id="canvas"></canvas>
            </div>
        </div>

    </div>
</template>

<script>

    import Chart from 'chart.js';

    export default {

        mounted() {
            this.init();
        },

        data() {
            return {
                busy : false,
                search : null,
                metrics : window.INITIAL_STATE.averageTimeOpen,
                resolution : 10,
                history : 1
            }
        },

        methods : {
            fetch() {
                this.busy = true;

                Api.get(`ticketsAverageTimeOpen/Group/OIT/${this.resolution}/${this.history}`)
                    .then(this.success, this.error)
            },

            init() {
                let context = document.getElementById("canvas").getContext('2d');

                let chrt = new Chart( context, {
                    type : 'line',
                    options: {
                        title : {
                            display: true,
                            text : 'Average Time To First Response (TTFR) Of Closed Tickets',
                        },
                        scales: {
                            xAxes: [{
                                scaleLabel : {
                                    display : true,
                                    labelString : 'Period'
                                }
                            }],
                            yAxes: [{
                                stacked: false,
                                scaleLabel : {
                                    display : true,
                                    labelString : 'Days (Smaller Is Better)'
                                }
                            }]
                        }
                    },
                    data : {
                        labels : this.metrics.data.map( o => o.min_date ),
                        datasets : [
                            // {
                            //     label : "Ticket Count",
                            //     data : this.metrics.data.map( o => o.count ),
                            //     // backgroundColor : ,
                            //     // borderColor : ,
                            // },
                            // {
                            //     label : "Min TTFR",
                            //     data : this.metrics.data.map( o => o.min_days_to_first_response ),
                            //     // backgroundColor : ,
                            //     // borderColor : ,
                            // },
                            // {
                            //     label : "Max TTFR",
                            //     data : this.metrics.data.map( o => o.max_days_to_first_response ),
                            //     // backgroundColor : ,
                            //     // borderColor : ,
                            // },
                            {
                                label : "Average TTFR (± 1σ)",
                                data : this.metrics.data.map( o => o.average_days_to_first_response_corrected_1 ),
                                backgroundColor : 'rgba(62, 209, 150, 0.05)',
                                borderColor : '#67dbac',
                            },
                            {
                                label : "Average TTFR (± 2σ)",
                                data : this.metrics.data.map( o => o.average_days_to_first_response_corrected_2 ),
                                backgroundColor : 'rgba(142, 180, 203, 0.05)',
                                borderColor : '#8eb4cb',
                            },
                            {
                                label : "Average TTFR (± 3σ)",
                                data : this.metrics.data.map( o => o.average_days_to_first_response_corrected_3 ),
                                backgroundColor : 'rgba(215, 109, 68, 0.05)',
                                borderColor : '#e08e6e',
                            },
                            {
                                label : "Average TTFR",
                                data : this.metrics.data.map( o => o.average_days_to_first_response ),
                                backgroundColor : 'rgba(48, 151, 209, 0.05)',
                                borderColor : '#5aacda',
                            },
                            // {
                            //     label : "Standard Dev (σ)",
                            //     data : this.metrics.data.map( o => o.std_dev ),
                            //     fill : false,
                            //     // backgroundColor : ,
                            //     // borderColor : ,
                            // },
                        ]
                    }
                });
            },

            success(response) {
                this.metrics = response.data;
                this.busy = false;
                this.init()
            },

            error(error) {
                console.error(error.response);
                this.busy = false;
            },
        },
    }
</script>

<style lang="scss">
    .ticket-metrics {
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0,0,0,0.3);

            display: flex;
            align-items: center;
            justify-content: center;

            i {
                font-size: 200px;
            }
        }
    }
</style>
