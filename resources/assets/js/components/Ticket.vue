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
        <td>{{ model.department }}/{{ model.customer }}</td>
        <td>{{ model.custom_fields.urgency }}</td>
        <td><span class="label" :class="[priorityClass]">{{ model.priority }}</span></td>
        <td><span class="label" :class="[statusClass]">{{ model.status }}</span></td>
        <template slot="menu">

        </template>


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
    export default {
        mixins : [
            mixins.item
        ],

        computed : {
            updated() {
                return fromNow(this.model.updated_at);
            },

            statusClass() {
                if ( this.model.status.$contains('Open') ) return 'label-danger';
                if ( this.model.status.$contains('Reopened') ) return 'label-danger';
                if ( this.model.status.$contains('Waiting') ) return 'label-warning';
                if ( this.model.status.$contains('Pending') ) return 'label-warning';
                if ( this.model.status.$contains('In Progress') ) return 'label-success';
                if ( this.model.status.$contains('Ready') ) return 'label-success';
                if ( this.model.status.$contains('Scheduled') ) return 'label-success';

                return 'label-warning';
            },

            priorityClass() {
                if ( this.model.priority.$contains('High') ) return 'label-danger';
                if ( this.model.priority.$contains('Medium') ) return 'label-warning';
                return 'label-success';
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
