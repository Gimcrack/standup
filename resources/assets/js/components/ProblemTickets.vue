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

                fetch_params : {},

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
                    heading : 'Absent Rep Tickets',
                    endpoint : 'ticketsProblem',
                    help : 'Tickets whose reps are absent. Use the In/Out Board button above to indicate who is absent.',
                    events : {
                        channel : 'users',
                        created : 'UserWasCreated',
                        destroyed : 'UserWasDestroyed',
                        global : {
                            ShowChecked : (val) => { this.show_checked = val },
                            AbsentPeople : (data) => {
                                console.log('Event Received');
                                this.absentPeople(data);

                            },
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
                Bus.$emit('ShowInOutBoard');
            },

            absentPeople(data) {
                this.fetch_params = {
                    reps : data.absent
                }

                this.details.modelProps.absent = data.absent;

                this.page.fetch();
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
