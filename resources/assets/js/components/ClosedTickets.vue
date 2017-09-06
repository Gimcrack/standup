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

                fetch_params : {

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
                    heading : 'Closed Tickets',
                    endpoint : 'ticketsClosed',
                    help : 'Tickets closed recently.',
                    events : {
                        channel : 'users',
                        created : 'UserWasCreated',
                        destroyed : 'UserWasDestroyed',
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
        },
    }
</script>
