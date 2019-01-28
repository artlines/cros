import React from 'react';
import {connect} from 'react-redux';
import {compose} from 'redux';
import { DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import {
    Grid,
    Typography,
    TextField,
    MenuItem,
    FormControl,
    FormControlLabel,
    Switch,
} from '@material-ui/core';
import map from 'lodash/map';
import f from 'lodash/filter';
import each from 'lodash/each';
import find from 'lodash/find';
import some from 'lodash/some';
import ApartmentBlock from '../../components/Abode/Settlement/ApartmentBlock';
import MemberInfoChipSource from "../../containers/DragDrop/MemberInfoChipSource";
import RoomBlockTarget from "../../containers/DragDrop/RoomBlockTarget";
import abode from "../../actions/abode";
import resettlement from "../../actions/resettlement";
import LinearProgress from "../../components/utils/LinearProgress";
import API from '../../libs/api';

const api = new API();

class Resettlement extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            filter: {
                apartment: {
                    room_type: 0,
                    only_not_full: false,
                },
                member: {
                    search: '',
                },
            },
        };
    }

    componentDidMount() {
        this.props.fetchRoomTypes();
        this.props.fetchApartmentTypes();
        this.update();
    }

    update = () => {
        this.props.fetchApartments();
        this.props.fetchNotSettledMembers();
    };

    handleFilterChange = (object, field) => event => {
        const { type, value, checked } = event.target;
        let filter = {...this.state.filter};

        switch (type) {
            case 'checkbox':
                filter[object][field] = checked;
                break;
            default:
                filter[object][field] = value;
                break;
        }

        this.setState({filter});
    };

    getApartments = () => {
        const { apartments, room_types } = this.props;
        const { filter } = this.state;
        let result = apartments.items;

        if (filter.apartment.room_type) {
            result = f(result, i => some(i.rooms, {type_id: filter.apartment.room_type}))
        }

        if (filter.apartment.only_not_full) {
            result = f(result, i =>
                some(i.rooms, room => {
                    const room_type = find(room_types, {id: room.type_id});
                    const isNotFull = room.places.length < room_type.max_places;
                    const isNeededType = filter.apartment.only_not_full
                        ? filter.apartment.room_type_id === room_type.id
                        : true;
                    console.log(isNeededType);

                    return isNotFull && isNeededType;
                })
            );
        }

        return result;
    };
    getMembers = () => {
        const { members } = this.props;
        const { filter } = this.state;
        let result = members.items;

        if (filter.member.search) {
            result = f(result, i =>
                `${i.first_name} ${i.last_name} ${i.org_name}`.toLowerCase().indexOf(filter.member.search.toLowerCase()) >= 0
            )
        }

        return result;
    };

    holdPlace = (room_id, conference_member_id) => {
        api.post(`place/new`, {room_id, conference_member_id})
            .then(this.update)
            .catch(err => console.log(`Error while holding place`, err.message));
    };
    changePlace = (place_id, room_id) => {
        api.put(`place/${place_id}`, {room_id})
            .then(this.update)
            .catch(err => console.log(`Error while changing place`, err.message));
    };
    dropPlace = (place_id) => {
        api.delete(`place/${place_id}`)
            .then(this.update)
            .catch(err => console.log(`Error while deleting place`, err.message));
    };

    render() {
        const { room_types, isFetching } = this.props;
        const { filter } = this.state;

        return (
            <React.Fragment>
                <LinearProgress show={isFetching}/>
                <br/>
                <Grid container spacing={16}>
                    <Grid item xs={8} sm={8} lg={9}>
                        <Typography gutterBottom variant={`h5`}>Номера</Typography>
                        <Grid container spacing={16} alignItems={`center`}>
                            <Grid item xs={12} sm={6} lg={4}>
                                <TextField
                                    required
                                    label={"Тип комнаты"}
                                    margin={"dense"}
                                    fullWidth
                                    value={filter.apartment.room_type}
                                    variant={"outlined"}
                                    name={'room_type'}
                                    onChange={this.handleFilterChange('apartment', 'room_type')}
                                    select={true}
                                >
                                    <MenuItem key={`all`} value={0}>{`Все`}</MenuItem>
                                    {map(room_types, rt =>
                                        <MenuItem key={rt.id} value={rt.id}>{rt.title}</MenuItem>
                                    )}
                                </TextField>
                            </Grid>
                            <Grid item xs={12} sm={6} lg={4}>
                                <FormControl>
                                    <FormControlLabel
                                        label={"Только не полностью заселенные"}
                                        control={
                                            <Switch
                                                checked={filter.apartment.only_not_full}
                                                onChange={this.handleFilterChange('apartment', 'only_not_full')}
                                            />
                                        }
                                    />
                                </FormControl>
                            </Grid>
                        </Grid>
                        <Grid container spacing={16}>
                            {map(this.getApartments(), apart =>
                                <Grid key={apart.id} item xs={12} sm={6} md={4} xl={3}>
                                    <ApartmentBlock
                                        apartment={apart}
                                        RoomComponent={RoomBlockTarget}
                                        roomComponentProps={{
                                            MemberComponent: MemberInfoChipSource,
                                            memberComponentProps: {
                                                holdPlace:      this.holdPlace,
                                                changePlace:    this.changePlace,
                                                dropPlace:      this.dropPlace,
                                            }
                                        }}
                                    />
                                </Grid>
                            )}
                        </Grid>
                    </Grid>
                    <Grid
                        item xs={4} sm={4} lg={3}
                        style={{ position: 'fixed', right: 0, }}
                    >
                        <Typography gutterBottom variant={`h5`}>Участники</Typography>
                        <Grid container spacing={16}>
                            <Grid item xs={12}>
                                <TextField
                                    required
                                    label={"Поиск по ФИО или организации"}
                                    margin={"dense"}
                                    fullWidth
                                    value={filter.member.search}
                                    variant={"outlined"}
                                    name={'search'}
                                    onChange={this.handleFilterChange('member', 'search')}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                {map(this.getMembers(), mb =>
                                    <MemberInfoChipSource
                                        key={mb.id}
                                        extendInfo
                                        member={mb}
                                        place={null}
                                        holdPlace={this.holdPlace}
                                        changePlace={this.changePlace}
                                        dropPlace={this.dropPlace}
                                    />
                                )}
                            </Grid>
                        </Grid>
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

const mapStateToProps = state =>
    ({
        ...state.resettlement,
        room_types: state.abode.room_type.items,
        isFetching: state.resettlement.apartments.isFetching || state.resettlement.members.isFetching,
        members: {
            isFetching: false,
            count: 0,
            items: [
                { id: 1, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2, neighbourhood: 'Фамилия Имя Отчество' },
                { id: 2, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 3, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 4, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2, neighbourhood: 'Фамилия Имя Отчество' },
                { id: 5, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2, neighbourhood: 'Фамилия Имя Отчество' },
                { id: 6, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 7, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 9, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2, neighbourhood: 'Фамилия Имя Отчество' },
                { id: 8, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 11, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 22, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 33, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2, neighbourhood: 'Фамилия Имя Отчество' },
                { id: 44, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 43, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2, neighbourhood: 'Фамилия Имя Отчество' },
                { id: 12, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 13, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 14, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2, neighbourhood: 'Фамилия Имя Отчество' },
                { id: 15, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 66, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
                { id: 17, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2, neighbourhood: 'Фамилия Имя Отчество' },
            ],
        },

    });

const mapDispatchToProps = (dispatch, ownProps) =>
    ({
        fetchRoomTypes: () => dispatch(abode.fetchRoomTypes()),
        fetchApartmentTypes: () => dispatch(abode.fetchApartmentTypes()),
        fetchNotSettledMembers: () => dispatch(resettlement.fetchNotSettledMembers()),

        fetchApartments: () => {
            const id = Number(ownProps.match.params.id);
            dispatch(resettlement.fetchApartments(id));
        },
    });

export default compose(
    connect(mapStateToProps, mapDispatchToProps),
    DragDropContext(HTML5Backend),
)(Resettlement);