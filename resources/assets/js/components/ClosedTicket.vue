<template>
    <item
        class="ticket"
        :id="model.id"
        :deleting="deleting"
        :updating="updating"
        :toggles="toggles"
        @view="view"
        @ToggledHasChanged="$emit('ToggledHasChanged')"
    >
        <td>{{ model.assignee }}</td>
        <td>{{ model.created_date }}</td>
        <td>{{ model.closed_date }}</td>
        <td>{{ model.category }}</td>
        <td>{{ model.department }}/{{ model.customer }}</td>

        <tr slot="row2">
            <td>&nbsp;</td>
            <td colspan="100">
                <div v-html="model.full_description" class="description">

                </div>
            </td>
        </tr>
    </item>
</template>

<script>
    import moment from 'moment';

    export default {
        mixins : [
            mixins.item
        ],

        computed : {

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

<style lang="scss">
    .ticket {
        .description {
            border: 1px solid #dedede;
            background: #efefef;
            border-radius: 4px;
            padding: 6px;

            font-size: 12px;
        }
    }

</style>
