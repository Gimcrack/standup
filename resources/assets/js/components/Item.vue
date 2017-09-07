<template>
    <tbody v-if="show" ref="row" :class="{sticky, toggled}">
        <tr>
            <td>
                <i @mouseover.prevent="checkToggle" @mousedown="toggle" style="cursor:pointer; font-size:1.5em; line-height:1" class="fa fa-fw" :class="[toggled ? ['fa-check-square-o','text-success'] : 'fa-square-o']"></i>
            </td>
            <slot name="pre"></slot>
            <td class="relative">
                <div class="btn-group">
                    <button @click.prevent.stop="show_menu = !show_menu" class="btn btn-xs btn-default btn-outline" :class="{ active : show_menu }">
                        <i class="fa fa-bars"></i>
                    </button>
                    <button @click.prevent="$emit('view')" class="btn btn-xs btn-default btn-outline">
                        {{ id }}
                    </button>
                </div>
                <div v-if="show_menu" class="btn-submenu">
                    <button v-if="toggles.info" @click.prevent.stop="$emit('view')" :disabled="busy" class="btn btn-info btn-xs btn-outline" :class="{disabled : busy}">
                        <i class="fa fa-fw fa-info"></i>
                    </button>
                    <button v-if="toggles.update" @click.prevent.stop="$emit('update')" :disabled="busy" class="btn btn-success btn-xs btn-outline" :class="{disabled : busy}">
                        <i class="fa fa-fw fa-refresh" :class="{'fa-spin' : updating}"></i>
                    </button>
                    <slot name="menu"></slot>
                    <button v-if="toggles.delete" @click.prevent.stop="$emit('destroy')" :disabled="busy" class="btn btn-danger btn-xs btn-outline" :class="{disabled : busy}">
                        <i class="fa fa-fw fa-times" :class="{'fa-spin' : deleting}"></i>
                    </button>
                </div>
            </td>
            <slot></slot>
        </tr>
        <slot name="row2"></slot>
    </tbody>
</template>

<script>
    export default {

        created() {
            this.$parent.$item = this;

            Bus.$on('ShowChecked', (val) => {
                this.show_checked = val;
                this.show = ( ! this.toggled || this.show_checked );
            });
        },

        props : {
            id : {
                required : true
            },
            updating : {
                default : false,
            },
            deleting : {
                default : false
            },
            sticky : {
                default : false
            },
            toggles: {
                default() {
                    return {
                        info : true,
                        update : true,
                        delete : true
                    }
                }
            }
        },

        data() {
            return {
                show_menu : false,
                toggled : false,
                show_checked : false,
                show : true
            }
        },

        computed : {
            busy() {
                return this.updating || this.deleting;
            }
        },

        methods: {
            toggle() {
                this.toggled = ! this.toggled;



                sleep(200).then(() => {
                    $('tbody.toggled.top').removeClass('top');
                    $('tbody.toggled.bottom').removeClass('bottom');

                    $('tbody.toggled').first().addClass('top');
                    $('tbody.toggled').last().addClass('bottom');

                    this.show = ( ! this.toggled || this.show_checked );
                });

                this.$emit('ToggledHasChanged');
            },

            checkToggle() {
                if ( window.mouseDown ) {
                    this.toggle();
                }
            }
        }
    }
</script>
