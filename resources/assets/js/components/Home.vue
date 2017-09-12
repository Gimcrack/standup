<template>
    <div id="home-container" class="container">
        <div class="">
            <div class="col-xs-1"> <!-- required for floating -->
                <!-- Nav tabs -->
                <ul class="nav nav-tabs tabs-left">
                  <li>
                    <a id="my" @click="nav('my',$event)">
                        <i class="fa fa-fw fa-2x fa-user"></i>
                    </a>
                  </li>
                  <li class="active">
                    <a id="hot" @click="nav('hot',$event)">
                        <i class="fa fa-fw fa-2x fa-circle text-success"></i>
                    </a>
                  </li>
                  <li>
                    <a id="closed" @click="nav('closed',$event)">
                        <i class="fa fa-fw fa-2x fa-check text-success"></i>
                    </a>
                  </li>
                  <li>
                    <a id="problem" @click="nav('problem',$event)">
                        <i class="fa fa-fw fa-2x fa-exclamation-circle text-danger"></i>
                    </a>
                  </li>
                  <li>
                    <a id="aging" @click="nav('aging',$event)">
                        <i class="fa fa-fw fa-2x fa-circle text-warning"></i>
                    </a>
                  </li>
                  <li>
                    <a id="stale" @click="nav('stale',$event)">
                        <i class="fa fa-fw fa-2x fa-circle text-danger"></i>
                    </a>
                  </li>
                  <li v-if="user.admin_flag">
                    <a id="users" @click="nav('users',$event)">
                        <i class="fa fa-fw fa-2x fa-users"></i>
                    </a>
                  </li>
                </ul>
            </div>

            <div class="col-xs-11">
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane" id="my">
                        <div>
                            <my-tickets></my-tickets>
                        </div>
                    </div>
                    <div class="tab-pane active" id="hot">
                        <div>
                            <tickets view="hot"></tickets>
                        </div>
                    </div>
                    <div class="tab-pane" id="closed">
                        <div>
                            <closed-tickets></closed-tickets>
                        </div>
                    </div>
                    <div class="tab-pane" id="problem">
                        <div>
                            <problem-tickets></problem-tickets>
                        </div>
                    </div>
                    <div class="tab-pane" id="aging">
                        <div>
                            <tickets view="aging"></tickets>
                        </div>
                    </div>
                    <div class="tab-pane" id="stale">
                        <div>
                            <tickets view="stale"></tickets>
                        </div>
                    </div>
                    <div v-if="user.admin_flag" class="tab-pane" id="users">
                        <div>
                            <users></users>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props : [
            'user'
        ],

        data() {
            return {

            }
        },

        mounted() {
            this.setInitialTab();

            this.$parent.home = this;

            window.addEventListener('popstate', (event) => {
                if ( event.state && event.state.tab )
                {
                    this.nav(event.state.tab);
                }
            });
        },

        methods : {

            nav(tab, event) {
                history.pushState( {tab : tab}, `ScrumBag - ${tab.$ucfirst()}`, `/#${tab}`);
                $('.nav-tabs').find('.active').removeClass('active').end().find(`#${tab}`).closest('li').addClass('active');
                $('.tab-content').find('.active').removeClass('active').end().find(`#${tab}`).addClass('active');
            },

            setInitialTab() {
                let tab = location.hash.replace('#','');
                if ( !! tab )
                {
                    this.nav( tab );
                }
            },

            showInOut() {
                Bus.$emit('ShowInOutBoard');
            }
        },
    }
</script>

<style lang="less">
    #home-container {
        background: white;
        width: 1400px;
        padding: 0;

        .col-xs-1 {
            padding: 0;
        }

        .nav-tabs li:not(.active) a {
            cursor: pointer;
        }
    }
</style>
