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
        <td v-if="! absent">{{ model.assignee }}</td>
        <td v-else><span class="label label-danger">{{ model.assignee }}</span></td>
        <td>{{ model.created_date }}</td>
        <td>{{ model.department }}/{{ model.customer }}</td>
        <td><span v-if="model.custom_fields.urgency" class="label" :class="[urgencyClass]">{{ model.custom_fields.urgency }}</span></td>
        <td><span class="label" :class="[priorityClass]">{{ model.priority }}</span></td>
        <td><span class="label" :class="[statusClass]">{{ model.status }}</span></td>
        <td>{{ score }}</td>
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
    import moment from 'moment';

    export default {
        mixins : [
            mixins.item
        ],

        computed : {
            ageScore() {
                let now = moment(),
                    age = now.diff( this.model.created_date, 'days' );

                if ( age > 14 ) return 10;
                if ( age > 12 ) return 9;
                if ( age > 10 ) return 8;
                if ( age > 8 ) return 7;
                if ( age > 6 ) return 5;
                if ( age > 4 ) return 4;
                if ( age > 2 ) return 2;
                if ( age > 0 ) return 1;
                return 0;
            },

            absent() {
                if ( ! this.modelProps.absent ) return false;

                return this.modelProps.absent.indexOf( this.model.assignee ) > -1;
            },

            statusScore() {
                let status = this.model.status || '';

                if ( status.$contains('Open') ) return 10;
                if ( status.$contains('Reopened') ) return 8;
                if ( status.$contains('Waiting') ) return 5;
                if ( status.$contains('Pending') ) return 4;
                if ( status.$contains('In Progress') ) return 2;
                if ( status.$contains('Ready') ) return 1;
                if ( status.$contains('Scheduled') ) return 1;

                return 1;
            },

            statusClass() {
                let status = this.model.status || '';

                if ( status.$contains('Open') ) return 'label-danger';
                if ( status.$contains('Reopened') ) return 'label-danger';
                if ( status.$contains('Waiting') ) return 'label-warning';
                if ( status.$contains('Pending') ) return 'label-warning';
                if ( status.$contains('In Progress') ) return 'label-success';
                if ( status.$contains('Ready') ) return 'label-success';
                if ( status.$contains('Scheduled') ) return 'label-success';

                return 'label-warning';
            },

            priorityScore() {
                let priority = this.model.priority || '';

                if ( priority.$contains('High') ) return 10;
                if ( priority.$contains('Medium') ) return 5;
                return 1;
            },

            priorityClass() {
                let priority = this.model.priority || '';

                if ( priority.$contains('High') ) return 'label-danger';
                if ( priority.$contains('Medium') ) return 'label-warning';
                return 'label-success';
            },

            urgencyScore() {
                let urgency = ( this.model.custom_fields ) ? this.model.custom_fields.urgency || '' : '';

                if ( urgency.$contains('Immediate') ) return 10;
                if ( urgency.$contains('Today') ) return 8;
                if ( urgency.$contains('Tomorrow') ) return 5;
                if ( urgency.$contains('This Week') ) return 3;
                if ( urgency.$contains('Next Week') ) return 2;
                return 1;
            },

            urgencyClass() {
                let urgency = ( this.model.custom_fields ) ? this.model.custom_fields.urgency || '' : '';

                if ( urgency.$contains('Immediate') ) return 'label-danger';
                if ( urgency.$contains('Today') ) return 'label-danger';
                if ( urgency.$contains('Tomorrow') ) return 'label-danger';
                if ( urgency.$contains('This Week') ) return 'label-warning';
                if ( urgency.$contains('Next Week') ) return 'label-warning';
                return 'label-success';
            },

            score() {
                return this.ageScore + this.statusScore + this.priorityScore + this.urgencyScore;
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
