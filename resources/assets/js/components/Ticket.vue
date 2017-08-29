<template>
    <item
        :id="model.id"
        :deleting="deleting"
        :updating="updating"
        :toggles="toggles"
        @view="view"
        @ToggledHasChanged="$emit('ToggledHasChanged')"
    >
        <td>{{ model.assignee }}</td>
        <td>{{ model.created_date }}</td>
        <td>{{ model.department }}/{{ model.customer }}</td>
        <td>{{ model.status }}</td>
        <template slot="menu">

        </template>
    </item>
</template>

<script>
    export default {
        mixins : [
            mixins.item
        ],

        computed : {
            updated() {
                return fromNow(this.model.updated_at);
            }
        },

        data() {
            return {
                item : {
                    key : 'id',
                    type : 'ticket',
                    endpoint : 'tickets',
                    channel : `tickets.${this.initial.id}`,
                    updated : 'TicketWasUpdated',
                },

                toggles : {
                    update : false,
                    delete : false,
                }
            }
        },

        methods : {
            postUpdated(event) {
                this.updating = false;
            },

            update() {

            },

            toggleAdmin() {
                if ( this.model.admin_flag )
                    return this.unpromote();

                return this.promote();
            },

            unpromote() {
                this.updating = true;

                Api.post(`users/${this.initial.id}/unpromote`)
                    .then(this.updateSuccess, this.error)
            },

            promote() {
                this.updating = true;

                Api.post(`users/${this.initial.id}/promote`)
                    .then(this.updateSuccess, this.error)
            },
        }
    }
</script>
