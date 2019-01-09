<template>
    <b-container>
        <router-link to="/players/new" class="btn btn-default btn-primary mt-4 float-right">Add Player</router-link>

        <b-table :items="players" :fields="fields" class="mt-4 float-left">
            <template slot="teams" slot-scope="row">
                <small>
                    {{playerTeamsDetailStr(row.item)}}
                </small>
            </template>
            <template slot="actions" slot-scope="row">
                <b-button size="sm" @click.stop="() => $router.push(`/players/edit/${row.item.id}`)" class="mr-1">
                    Edit
                </b-button>
                <b-button size="sm" @click.stop="deleteModal(row.item)" class="mr-1">
                    Delete
                </b-button>
            </template>
        </b-table>

        <b-modal id="deleteModal" title="Are you sure to remove this team?" @ok="deletePlayer">
            <small>This player will automatically be unassociated from their current team(s).</small>
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
                    {key: 'first_name', label: 'First Name', sortable: true, sortDirection: 'asc'},
                    {key: 'last_name', label: 'Last Name', sortable: true},
                    {key: 'teams', label: 'Current Teams'},
                    {key: 'actions', label: 'Actions'}
                ],
                currentPlayerTeams: [],
                currentPlayerId: null
            }
        },
        computed: {
            ...mapState({
                players: state => state.players.items,
                isLoading: state => state.players.isLoading,
                hasError: state => state.players.hasError,
                loaded: state => state.players.loaded,
                applyingChanges: state => state.players.applyingChanges,
            }),
        },
        created() {
            this.loadPlayers();
        },

        methods: {
            loadPlayers() {
                return this.$store.dispatch('players/retrieveItems', {
                    params: {}
                });
            },
            deleteModal(item) {
                this.currentPlayerId = item.id;
                this.$root.$emit('bv::show::modal', 'deleteModal');
            },
            deletePlayer() {
                return this.$store.dispatch('players/deleteItem', {
                    id: this.currentPlayerId,
                    onFinished: (success) => {
                        if (success) {
                            window.toastr.success('Player removed successfully', 'Success');
                            this.loadPlayers();
                        } else {
                            window.toastr.error('Error while removing player', 'Error');
                        }
                    }
                });
            },
            playerTeamsDetailStr(player) {
                let teamsDetails = [];

                if (player && player['player_teams']) {
                    player['player_teams'].forEach(playerTeam => {
                        if (playerTeam['team']) {
                            teamsDetails.push(`${playerTeam['team']['name']} (${playerTeam['position']})`);
                        }
                    });
                }

                return teamsDetails.join(', ');
            }
        }
    }
</script>
