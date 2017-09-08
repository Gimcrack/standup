<template>
    <page
        :params="details"
        :toggles="toggles"
    >

        <template slot="menu">
            <button @click.prevent="toggleChecked" class="btn btn-primary">
                <i class="fa fa-fw fa-check" :class="[ show_checked ? 'fa-toggle-on' : 'fa-toggle-off', { active : show_checked } ]"></i>
                Toggle Checked
            </button>
        </template>
    </page>
</template>

<script>
    export default {

        props : [
            'view'
        ],

        data() {
            return {

                show_checked : false,

                toggles : {
                    new : false,
                    delete : false,
                    update : false
                },

                fetch_params : {

                },

                details : {
                    columns : [
                        'number',
                        'assignee',
                        'created_date',
                        'closed_date',
                        'category',
                        'customer',
                    ],
                    type : 'closedTicket',
                    heading : 'Recently Closed Tickets',
                    endpoint : 'ticketsClosed',
                    help : 'Tickets closed recently.',
                    events : {
                        channel : 'users',
                        created : 'UserWasCreated',
                        destroyed : 'UserWasDestroyed',
                        global : {
                            ShowChecked : (val) => { this.show_checked = val }
                        }
                    },
                    data_key : 'data',
                    order : 'score',
                    model_friendly : 'number'
                },

                tempUser : {
                    name : null,
                    email : null,
                    password : null
                },


            }
        },

        methods : {

            showInOut() {
                Bus.$emit('showInOut');
            },

            toggleChecked() {
                this.show_checked = ! this.show_checked;

                Bus.$emit('ShowChecked', this.show_checked);
            }
        },
    }
</script>
