<template>
    <div v-if="visible" class="in-out-board-wrapper">
        <form @submit.prevent="submit">
            <div class="in-out-board">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="text-success">
                            <i class="fa fa-fw fa-user"></i>
                            In/Out Board
                        </span>
                    </div>
                    <div class="panel-body">

                        <div class="alert alert-warning"><i class="fa fa-fw fa-exclamation-circle"></i>
                            <strong>Note</strong> Uncheck people who are not here.
                        </div>

                        <input v-model="filter" placeholder="Quick Find" type="text" class="form-control">

                        <ul class="rep-list" v-if="done">
                            <rep @RepInOut="report" v-for="rep in filtered_reps" :name="rep" :key="rep"></rep>
                        </ul>
                        <div v-if="busy">
                            <h1><i class="fa fa-fw fa-refresh fa-spin"></i></h1>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="btn-group">
                            <button :disabled="busy" :class="{disabled:busy}" type="submit" class="btn btn-success btn-outline">Go</button>
                            <button :disabled="busy" :class="{disabled:busy}" type="button" @click.prevent="cancel" class="btn btn-danger btn-outline">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.listen();
        },

        components : {
            rep : require('./Rep.vue')
        },

        data() {
            return {
                done : false,
                reps : [],
                reported : [],
                visible : false,
                filter : null,
                password : null,
                password_confirmation : null,
                master_password : null,
                client : null,
                busy : false
            }
        },

        computed : {
            filtered_reps() {
                if (! this.filter) return this.reps;

                return _(this.reps).filter( name => name.toLowerCase().indexOf(this.filter.toLowerCase()) > -1).value();
            },

            absent() {
                return _(this.reported).filter( rep => ! rep.here ).pluck('name').value();
            },
        },

        methods : {
            fetch() {
                this.busy = true;

                Api.get('assignees')
                    .then( this.success, this.error )
            },

            listen() {
                Bus.$on('ShowInOutBoard', (event) => {
                    this.show();
                })
            },

            report(rep) {
                let i = this.reported.findIndex(o => o.name == rep.name);

                if ( i > -1 ) {
                    if ( rep.here ) {
                        Vue.delete(this.reported,i);
                    }
                    else {
                        this.reported[i] = rep;
                    }
                }
                else {
                    this.reported.push(rep)
                }

                this.$forceUpdate();
                return;
            },

            show() {
                if ( ! this.done )
                    this.fetch();

                this.visible = true;
            },

            cancel() {
                this.visible = false;
                this.resetForm();
            },

            resetForm() {
                this.busy = false;
            },

            submit() {
                Bus.$emit('AbsentPeople', { absent : this.absent });

                this.cancel();
            },

            ignore() {

            },

            success(response) {
                this.busy = false;
                this.reps = response.data.persons;
                this.done = true;
            },

            error(error) {
                let message = ( !! error.response.data.password ) ? error.response.data.password[0]  : 'There was an error performing the operation. See the console for more details';
                flash.error(message);
                console.error(error.response);

                this.busy = false;
            },
        }


    }
</script>

<style lang="less">
    .in-out-board {
        width: 700px;
        min-height: 400px;

        .panel-heading {
            font-size: 24px;
        }

        .panel-footer {
            display: flex;
            justify-content: flex-end;
        }

        .panel-body {
            button {
                font-weight: bold;
            }
        }

        .rep-list {
            list-style: none;
            column-count: 2;

            li {
                list-style: none;
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

        .form-control {
            width: 100%;
            margin-bottom: 15px;
        }
    }

    .in-out-board-wrapper {
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
