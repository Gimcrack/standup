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

                details : {
                    columns : [
                        'number',
                        'assignee',
                        'created_date',
                        'customer',
                        'urgency',
                        'priority',
                        'status',
                        'score'
                    ],
                    type : 'ticket',
                    heading : 'Tickets - ' + this.view.$ucfirst(),
                    endpoint : 'tickets' + this.view.$ucfirst(),
                    help : this.helpMessage(),
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
                }
            }
        },

        methods : {

            showInOut() {
                Bus.$emit('showInOut');
            },

            helpMessage() {
                switch(this.view) {
                    case 'hot' :
                        return 'Unclosed tickets that were created recently.'

                    case 'aging' :
                        return 'Unclosed tickets that were created in the last week.'

                    case 'stale' :
                        return 'Unclosed tickets older than one week.'
                }
            },

            toggleChecked() {
                this.show_checked = ! this.show_checked;

                Bus.$emit('ShowChecked', this.show_checked);
            }
        },
    }
</script>
