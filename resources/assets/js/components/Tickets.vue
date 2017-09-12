<template>
    <page
        :params="details"
        :toggles="toggles"
    >
        <template slot="menu">
            <toggle @clicked="toggleChecked" :active="show_checked">Checked</toggle>
            <toggle @clicked="toggleOpen" :active="show_open">Open Only</toggle>
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
                show_open : false,

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
                            ShowChecked : (val) => { this.show_checked = val },
                            AbsentPeople : (e) => { this.details.modelProps.absent = e.absent },
                        }
                    },
                    data_key : 'data',
                    order : 'score',
                    model_friendly : 'number',
                    modelProps : {
                        absent : []
                    },
                    where : {}
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
            },

            toggleOpen() {
                this.show_open = ! this.show_open;

                this.details.where = ( this.show_open ) ? { status : 'Open' } : {};
            }
        },
    }
</script>
