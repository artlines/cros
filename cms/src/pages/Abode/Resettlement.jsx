import React from 'react';
import {connect} from 'react-redux';
import {compose} from 'redux';
import { DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import {
    Grid,
    Divider,
    Typography,
    TextField,
    MenuItem,
    FormControl,
    FormControlLabel,
    Switch,
} from '@material-ui/core';
import map from 'lodash/map';
import f from 'lodash/filter';
import find from 'lodash/find';
import some from 'lodash/some';
import every from 'lodash/every';
import isEqual from 'lodash/isEqual';
import ApartmentBlock from '../../components/Abode/Settlement/ApartmentBlock';
import MemberInfoSource from "../../containers/DragDrop/MemberInfoSource";
import RoomBlockTarget from "../../containers/DragDrop/RoomBlockTarget";
import MembersListTarget from "../../containers/DragDrop/MembersListTarget";
import abode from "../../actions/abode";
import resettlement from "../../actions/resettlement";
import LinearProgress from "../../components/utils/LinearProgress";
import API from '../../libs/api';
import MemberInfoListItem from "../../components/Abode/Settlement/MemberInfoListItem";

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
            },
            available_room_types: [],
        };
    }

    componentDidMount() {
        this.props.fetchRoomTypes();
        this.props.fetchApartmentTypes();
        this.update();
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { housing } = this.props;

        if (!isEqual(housing, prevProps.housing) && !housing.isFetching) {
            this.setState({available_room_types: map(housing.abode_info, i => i.room_type_id)});
        }
    }

    update = () => {
        this.props.fetchApartments();
        this.props.fetchNotSettledMembers();
        this.props.fetchHousing();
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
            result = f(result, i => {
                if (filter.apartment.room_type) {
                    const room_type = find(room_types, {id: filter.apartment.room_type});
                    const filtered = f(i.rooms, g => g.type_id === filter.apartment.room_type);
                    return !every(filtered, room => room.places.length >= room_type.max_places);
                } else {
                    return !every(i.rooms, room => {
                        const room_type = find(room_types, {id: room.type_id});
                        return room.places.length >= room_type.max_places;
                    })
                }
            });
        }

        return result;
    };

    getMembers = () => {
        const { members } = this.props;
        const { filter } = this.state;
        let result = members.items;

        if (filter.apartment.room_type) {
            result = f(result, i => i.room_type_id === filter.apartment.room_type)
        }

        return result;
    };

    getAbodeInfo = () => {
        const { housing } = this.props;
        const { filter: { apartment } } = this.state;
        const info = find(housing.abode_info, {room_type_id: apartment.room_type});

        return (info &&
            <React.Fragment>
                <Typography>Свободно / Резерв / Занято / Всего</Typography>
                <Typography>{info.total - info.busy - info.reserved} / {info.reserved} / {info.busy} / {info.total}</Typography>
            </React.Fragment>
        );
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
        const { housing, room_types, isFetching } = this.props;
        const { filter: { apartment }, available_room_types } = this.state;

        return (
            <React.Fragment>
                <LinearProgress show={isFetching}/>
                <br/>
                <Grid container spacing={16}>
                    <Grid item xs={8} sm={8} lg={9}>
                        <Typography gutterBottom variant={`h5`}>{housing.title}</Typography>
                        <Grid container spacing={16} alignItems={`center`}>
                            <Grid item xs={12} sm={6} lg={4}>
                                <TextField
                                    required
                                    label={"Тип комнаты"}
                                    margin={"dense"}
                                    fullWidth
                                    value={apartment.room_type}
                                    variant={"outlined"}
                                    name={'room_type'}
                                    onChange={this.handleFilterChange('apartment', 'room_type')}
                                    select={true}
                                >
                                    <MenuItem key={`all`} value={0}>{`Все`}</MenuItem>
                                    {map(f(room_types, rt => available_room_types.includes(rt.id)), rt =>
                                        <MenuItem key={rt.id} value={rt.id}>{rt.title}</MenuItem>
                                    )}
                                </TextField>
                            </Grid>
                            <Grid item xs={12} sm={6} lg={4}>
                                <FormControl>
                                    <FormControlLabel
                                        label={"Только свободные"}
                                        control={
                                            <Switch
                                                checked={apartment.only_not_full}
                                                onChange={this.handleFilterChange('apartment', 'only_not_full')}
                                            />
                                        }
                                    />
                                </FormControl>
                            </Grid>
                            {apartment.room_type !== 0 &&
                                <Grid item xs={12} sm={6} lg={4}>
                                    {this.getAbodeInfo()}
                                </Grid>
                            }
                        </Grid>
                        <br/>
                        <Grid container spacing={16}>
                            {map(this.getApartments(), apart =>
                                <Grid key={apart.id} item xs={12} sm={6} md={4} xl={3}>
                                    <ApartmentBlock
                                        apartment={apart}
                                        RoomComponent={RoomBlockTarget}
                                        roomComponentProps={{
                                            MemberComponent: MemberInfoSource,
                                            memberComponentProps: {
                                                holdPlace:      this.holdPlace,
                                                changePlace:    this.changePlace,
                                                dropPlace:      this.dropPlace,
                                                Component:      MemberInfoListItem,
                                                dense:          true,
                                            }
                                        }}
                                    />
                                </Grid>
                            )}
                        </Grid>
                    </Grid>
                    <Grid
                        item xs={4} sm={4} lg={3}
                        style={{ position: 'fixed', right: 0, width: '100%' }}
                    >
                        <MembersListTarget
                            members={this.getMembers()}
                            MemberComponent={MemberInfoSource}
                            memberComponentProps={{
                                holdPlace:      this.holdPlace,
                                changePlace:    this.changePlace,
                                dropPlace:      this.dropPlace,
                                Component:      MemberInfoListItem,
                            }}
                        />
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

const mapStateToProps = state =>
    ({
        ...state.resettlement,
        housing: state.abode.housing.item,
        room_types: state.abode.room_type.items,
        isFetching: state.resettlement.apartments.isFetching
            || state.resettlement.members.isFetching
            || state.abode.housing.item.isFetching,
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

        fetchHousing: () => {
            const id = Number(ownProps.match.params.id);
            dispatch(abode.fetchHousing(id));
        },
    });

export default compose(
    connect(mapStateToProps, mapDispatchToProps),
    DragDropContext(HTML5Backend),
)(Resettlement);