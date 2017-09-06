<template>
    <li class="rep" @click="toggle">
        <i class="fa fa-fw" :class="iClass"></i>
        {{ name }}
    </li>
</template>

<script>
    export default {
        props : [
            'name'
        ],

        data() {
            return {
                here : true
            }
        },

        methods : {
            toggle() {
                this.here = ! this.here;

                this.$emit('RepInOut',{
                    name : this.name,
                    here : this.here
                });
            }
        },

        computed : {
            iClass() {
                if (this.here) {
                    return ['fa-check-square-o','text-success'];
                }

                return 'fa-square-o';
            }
        }
    }
</script>

<style lang="less">
    .rep {
        cursor: pointer;
        font-size: 24px;
        line-height: 1.5;
    }
</style>
