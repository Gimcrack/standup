<template>
    <div v-if="visible" class="item-detail-wrapper">
        <div class="item-detail">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="text-success">
                        <i class="fa fa-fw fa-exclamation-triangle"></i>
                        Ticket Detail
                    </span>
                </div>
                <div v-if="! busy" class="panel-body">
                    <iframe :src="url" frameborder="0"></iframe>
                </div>
                <div v-else class="panel-body">
                    <i class="fa fa-fw fa-refresh fa-spin fa-5x"></i>
                </div>
                <div class="panel-footer">
                    <div class="btn-group">
                        <button :disabled="busy" :class="{disabled:busy}" type="button" @click.prevent="cancel" class="btn btn-primary btn-outline">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import pretty from 'prettyprint';

    export default {
        mounted() {
            this.listen();
        },

        data() {
            return {
                visible : false,
                busy : false,
                endpoint : null,
                id : null,
                detail : null
            }
        },

        computed : {
            url() {
                return `https://isupport.matsugov.us/Rep/Incident/default.aspx?ID=${this.id}`;
            }
        },

        methods : {
            listen() {
                Bus.$on('ShowItemDetail', (event) => {
                    this.resetDetail();
                    this.endpoint = event.endpoint;
                    this.id = event.id;
                    this.show();
                });
            },

            show() {
                this.visible = true;
            },

            cancel() {
                this.visible = false;
                this.resetDetail();
            },

            resetDetail() {
                this.busy = false;
                this.endpoint = null;
                this.id = null;
                this.model = {};
            },

            ignore() {

            },

            success( response ) {
                this.busy = false;
                this.detail = response.data;
            },

            error(error) {
                this.busy = false;
                let message = 'There was an error performing the operation. See the console for more details';
                flash.error(message);
                console.error(error.response);

            },
        }
    }
</script>

<style lang="less">
    .item-detail {
        width: 90%;
        min-height: 90%;

        .panel-heading {
            font-size: 24px;
        }

        .panel-footer {
            display: flex;
            justify-content: flex-end;
        }

        .panel-body {
            height: 76vh;
            overflow-y: scroll;
            padding: 0 1em;
            button {
                font-weight: bold;
            }

            iframe {
                width: 100%;
                height: 100%;
                margin: 1em 0;
            }
        }

        .partial-path-form {
            display: flex;

            input {
                flex: 1;
            }

            * + * {
                margin-left: 15px;
            }
        }

    }

    .item-detail-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        height: 100vh;
        width: 100vw;
        background: rgba(0,0,0,0.3);

        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
