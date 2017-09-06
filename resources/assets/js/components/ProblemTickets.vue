<template>
    <page
        :params="details"
        :toggles="toggles"
    >
    </page>
</template>

<script>
    export default {

        props : [
            'view'
        ],

        data() {
            return {
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
                    heading : 'At Risk Tickets',
                    endpoint : 'ticketsProblem',
                    help : 'Tickets whose reps are absent. Use the In/Out Board button above to indicate who is absent.',
                    events : {
                        channel : 'users',
                        created : 'UserWasCreated',
                        destroyed : 'UserWasDestroyed',
                        global : {
                            AbsentPeople : (data) => {
                                console.log('Event Received');
                                this.absentPeople(data)
                            }
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
                Bus.$emit('ShowInOutBoard');
            },

            absentPeople(data) {
                this.fetch_params = {
                    reps : data.absent
                }

                this.page.fetch();
            }
        },
    }
</script>
