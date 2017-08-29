<template>
    <page
        :params="details"
        :toggles="toggles"
    ></page>
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

                details : {
                    columns : [
                        'number',
                        'assignee',
                        'created_date',
                        'customer',
                        'status',
                    ],
                    type : 'ticket',
                    heading : 'Tickets - ' + this.view.$ucfirst(),
                    endpoint : 'tickets' + this.view.$ucfirst(),
                    help : this.helpMessage(),
                    events : {
                        channel : 'users',
                        created : 'UserWasCreated',
                        destroyed : 'UserWasDestroyed',
                    },
                    data_key : 'data'
                },

                tempUser : {
                    name : null,
                    email : null,
                    password : null
                }
            }
        },

        methods : {

            helpMessage() {
                switch(this.view) {
                    case 'hot' :
                        return 'Unclosed tickets that were created recently.'

                    case 'aging' :
                        return 'Unclosed tickets that were created in the last week.'

                    case 'stale' :
                        return 'Unclosed tickets older than one week.'
                }
            }
        },
    }
</script>
