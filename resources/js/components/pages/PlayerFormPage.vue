<template>
    <b-container class="mt-4">
        <b-form v-if="!playerNotFound">
            <b-form-row>
                <b-col sm="6">
                    <b-form-group label="Player First Name:">
                        <b-form-input v-model="form.first_name" required placeholder="Enter first name"></b-form-input>
                    </b-form-group>
                    <b-form-group label="Player Last Name:">
                        <b-form-input v-model="form.last_name" required placeholder="Enter last name"></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="6" class="pl-4">
                    <label class="col-form-label">Associated teams:</label>

                    <b-table :items="this.form.teams" class="table-sm"
                             :fields="['team_name', 'position', 'number', 'actions']">
                        <template slot="actions" slot-scope="row">
                            <b-button size="sm" variant="link" @click.stop="editTeamModal(row.item)">Edit</b-button>
                            <b-button size="sm" variant="link" @click.stop="removeTeam(row.item.team_id)">Remove</b-button>
                        </template>
                    </b-table>

                    <b-button size="sm" @click.stop="editTeamModal(null)">Add team</b-button>

                    <b-modal id="editTeamModal" title="Add/Edit team" @ok="lockTeamAssociation">
                        <b-form-group label="Team:" v-if="!editingTeam.team_id">
                            <b-form-select v-model="editingTeam.team_id" :options="teamsSelectList" size="sm">
                            </b-form-select>
                        </b-form-group>

                        <label class="col-form-label" v-if="editingTeam.team_id">
                            Editing details for team: <strong>{{editingTeam.team_name}}</strong>
                        </label>

                        <b-form-group label="Position:">
                            <b-form-select v-model="editingTeam.position" :options="validPositions" size="sm">
                            </b-form-select>
                        </b-form-group>
                        <b-form-group label="Number:">
                            <b-form-input required type="number" v-model="editingTeam.number"></b-form-input>
                        </b-form-group>
                    </b-modal>
                </b-col>
            </b-form-row>

            <b-button @click="onSubmit" variant="primary">Save</b-button>
            <b-button @click="() => $router.push('/players')">Cancel</b-button>
        </b-form>

        <h3 v-if="playerNotFound">Player not found</h3>

        <loading-overlay :active="applyingChanges || isLoading" :is-full-page="true"></loading-overlay>
    </b-container>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        data() {
            return {
                form: {
                    first_name: '',
                    last_name: '',
                    teams: []
                },
                playerNotFound: false,
                editingTeam: {team_id: null, position: null, number: null},
                validPositions: ['GK', 'DF', 'MF', 'FW'],
                teamsSelectList: [],
            }
        },
        watch: {
            'editingTeam.team_id': {
                handler(teamId) {
                    if (typeof this.editingTeam['team_name'] === 'undefined') {
                        const teamOptions = this.teamsSelectList.filter(team => {
                            return team.value === teamId;
                        });

                        if (teamOptions.length) {
                            this.editingTeam['team_name'] = teamOptions[0]['text'];
                        }
                    }
                }
            },
            'teams': {
                handler() {
                    this.fetchTeamsSelectList();
                }
            },
            'form.teams': {
                handler() {
                    this.fetchTeamsSelectList();
                }
            }
        },
        created() {
            this.loadTeams();
            this.loadPlayer();
        },
        computed: {
            ...mapState({
                teams: state => state.teams.items,
                players: state => state.players.items,
                isLoading: state => state.players.isLoading,
                hasError: state => state.players.hasError,
                loaded: state => state.players.loaded,
                applyingChanges: state => state.players.applyingChanges,
            }),
        },
        methods: {
            loadTeams() {
                return this.$store.dispatch('teams/retrieveItems', {
                    params: {}
                });
            },
            loadPlayer() {
                if (this.$route.params['id']) {
                    return this.$store.dispatch('players/findItem', {
                        id: this.$route.params['id'],
                        onFinished: (success, data) => {
                            if (success) {
                                this.form.first_name = data.first_name;
                                this.form.last_name = data.last_name;
                                this.form.teams = data['player_teams'].map(item => {
                                    return {
                                        team_id: item.team_id,
                                        team_name: item['team'].name,
                                        position: item.position,
                                        number: item.number
                                    }
                                });
                            } else {
                                if (data.response && data.response.status === 404) {
                                    this.playerNotFound = true;
                                } else {
                                    window.toastr.error('Error while loading player', 'Error');
                                }
                            }
                        }
                    });
                }
            },
            onSubmit(evt) {
                evt.preventDefault();
                return this.$store.dispatch('players/updateOrCreate', {
                    data: this.form,
                    id: this.$route.params['id'] || null,
                    onFinished: (success) => {
                        if (success) {
                            window.toastr.success('Player saved successfully', 'Success');
                            this.$router.push({path: '/players'});
                        } else {
                            window.toastr.error('Error while saving player', 'Error');
                        }
                    }
                });
            },
            lockTeamAssociation() {
                const teamId = this.editingTeam.team_id;
                let teamIndex = 0;
                let playerTeams = Object.assign([], this.form.teams);

                for (let i = 0; i < playerTeams.length; i++) {
                    if (playerTeams[i].team_id === teamId) {
                        break;
                    }

                    teamIndex++;
                }


                if (typeof playerTeams[teamIndex] === 'undefined') {
                    playerTeams[teamIndex] = {};
                }

                playerTeams[teamIndex] = Object.assign(playerTeams[teamIndex], this.editingTeam);
                this.form.teams = playerTeams;

                this.editingTeam = {team_id: null, position: null, number: null};
            },
            removeTeam(teamId) {
                this.form.teams = this.form.teams.filter(team => team.team_id !== teamId);
            },
            editTeamModal(editingTeam) {
                if (editingTeam) {
                    this.editingTeam = editingTeam;
                } else {
                    this.editingTeam = {team_id: null, position: null, number: null};
                }

                this.$root.$emit('bv::show::modal', 'editTeamModal');
            },
            fetchTeamsSelectList() {
                this.teamsSelectList = this.teams.filter(team => {
                    return !this.form.teams.map(playerTeam => playerTeam['team_id']).includes(team.id);
                }).map(team => ({value: team.id, text: team.name}))
            }
        }
    }
</script>
