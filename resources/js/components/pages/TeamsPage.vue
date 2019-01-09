<template>
    <b-container>
        <router-link to="/teams/new" class="btn btn-default btn-primary mt-4 float-right">Add Team</router-link>

        <b-table :items="teams" :fields="fields" class="mt-4 float-left">
            <template slot="type" slot-scope="row">
                {{row.value.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')}}
            </template>
            <template slot="actions" slot-scope="row">
                <b-button size="sm" @click.stop="() => $router.push(`/teams/edit/${row.item.id}`)" class="mr-1">
                    Edit
                </b-button>
                <b-button size="sm" @click.stop="playersListModal(row.item, row.index, $event.target)" class="mr-1">
                    Show Players
                </b-button>
                <b-button size="sm" @click.stop="deleteModal(row.item)" class="mr-1">
                    Delete
                </b-button>
            </template>
        </b-table>

        <b-modal id="playersListModal" title="Players List" ok-only>
            <b-table :items="currentTeamPlayers"></b-table>
            <span v-if="currentTeamPlayers.length === 0">No players associated</span>
        </b-modal>

        <b-modal id="deleteModal" title="Are you sure to remove this team?" @ok="deleteTeam">
            <small>Associated players will not be removed.</small>
        </b-modal>

        <loading-overlay :active="applyingChanges || isLoading" :is-full-page="true"></loading-overlay>
    </b-container>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        data() {
            return {
                fields: [
                    {key: 'name', label: 'Team Name', sortable: true, sortDirection: 'asc'},
                    {key: 'type', label: 'Team Type', sortable: true},
                    {key: 'actions', label: 'Actions'}
                ],
                currentTeamPlayers: [],
                currentTeamId: null
            }
        },
        computed: {
            ...mapState({
                teams: state => state.teams.items,
                isLoading: state => state.teams.isLoading,
                hasError: state => state.teams.hasError,
                loaded: state => state.teams.loaded,
                applyingChanges: state => state.teams.applyingChanges,
            }),
        },
        created() {
            this.loadTeams();
        },

        methods: {
            loadTeams() {
                return this.$store.dispatch('teams/retrieveItems', {
                    params: {}
                });
            },
            playersListModal(item, index, button) {
                this.currentTeamPlayers = item['players'].map(player => ({
                    name: `${player['first_name']} ${player['last_name']}`,
                    position: player['player_teams'].filter(pt => pt['team_id'] === item.id).map(pt => pt['position']).join(','),
                    number: player['player_teams'].filter(pt => pt['team_id'] === item.id).map(pt => pt['number']).join(','),
                }));

                this.$root.$emit('bv::show::modal', 'playersListModal', button);
            },
            deleteModal(item) {
                this.currentTeamId = item.id;
                this.$root.$emit('bv::show::modal', 'deleteModal');
            },
            deleteTeam() {
                return this.$store.dispatch('teams/deleteItem', {
                    id: this.currentTeamId,
                    onFinished: (success) => {
                        if (success) {
                            window.toastr.success('Team removed successfully', 'Success');
                            this.loadTeams();
                        } else {
                            window.toastr.error('Error while removing team', 'Error');
                        }
                    }
                });
            },
            toggleShowPlayers(team) {
                let index = this.displayingPlayers.indexOf(team.id);

                if (index !== -1) {
                    this.displayingPlayers.splice(index, 1);
                } else {
                    this.displayingPlayers.push(team);
                }
            }
        }
    }
</script>
