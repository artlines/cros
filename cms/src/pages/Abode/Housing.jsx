import React from 'react';
import {connect} from 'react-redux';
import abode from '../../actions/abode';
import {
    Grid,
    Typography,
    LinearProgress,
} from '@material-ui/core';
import CompareArrowsIcon from '@material-ui/icons/CompareArrows';
import FabButton from '../../components/utils/FabButton';
import ApartmentsAddForm from '../../components/Abode/ApartmentsAddForm';
import ChangeRoomsTypesForm from "../../components/Abode/ChangeRoomsTypesForm";
import ApartmentsTable from "../../components/Abode/ApartmentsTable";

class Housing extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            ApartmentsAdd: {
                initialValues: {},
                open: false,
            },
            ChangeRoomsTypes: {
                initialValues: {},
                open: false,
            },
        };
    }

    componentDidMount() {
        this.props.loadData();
        this.props.fetchHousing();
        this.props.fetchApartments();
        this.props.fetchRooms();
    }

    componentDidUpdate() {
        console.log(`Housing::componentDidUpdate`);
    }

    openApartmentsAddForm = () => {
        const { housing: { id: housing_id } } = this.props;
        this.setState({
            ApartmentsAdd: {
                ...this.state.ApartmentsAdd,
                open: true,
                initialValues: {housing_id},
            },
        });
    };
    handleCloseApartmentsAddForm = () => this.setState({ApartmentsAdd: {...this.state.ApartmentsAdd, open: false}});

    openChangeRoomsTypesForm = () => this.setState({ChangeRoomsTypes: { ...this.state.ChangeRoomsTypes, open: true }});
    handleCloseChangeRoomsTypesForm = () => this.setState({ChangeRoomsTypes: {...this.state.ChangeRoomsTypes, open: false}});

    render() {
        const {
            housing: { isFetching, error, id, title },
            apartment, room,
            fetchRooms, fetchApartments
        } = this.props;
        const { ApartmentsAdd, ChangeRoomsTypes } = this.state;

        if (isFetching && !id) {
            return (<LinearProgress/>);
        }

        return (
            <div>
                <ApartmentsAddForm
                    open={ApartmentsAdd.open}
                    initialValues={ApartmentsAdd.initialValues}
                    onClose={this.handleCloseApartmentsAddForm}
                    onSuccess={() => {
                        fetchRooms();
                        fetchApartments();
                    }}
                />
                <ChangeRoomsTypesForm
                    open={ChangeRoomsTypes.open}
                    initialValues={ChangeRoomsTypes.initialValues}
                    onClose={this.handleCloseChangeRoomsTypesForm}
                    onSuccess={() => {
                        fetchRooms();
                        fetchApartments();
                    }}
                />
                <Grid container spacing={24}>
                    <Grid item xs={12}>
                        <Grid container spacing={0} justify={`space-between`}>
                            <Grid item>
                                <Typography variant={`h5`} component={`span`}>{title}</Typography>
                            </Grid>
                            <Grid item>
                                <Grid container spacing={8}>
                                    <Grid item>
                                        <FabButton
                                            title={`Добавить номера`}
                                            onClick={this.openApartmentsAddForm}
                                        />
                                    </Grid>
                                    <Grid item>
                                        <FabButton
                                            title={`Смена типа комнат`}
                                            onClick={this.openChangeRoomsTypesForm}
                                            IconComponent={CompareArrowsIcon}
                                        />
                                    </Grid>
                                </Grid>
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid item xs={12}>
                        {(apartment.isFetching || room.isFetching)
                            ? <LinearProgress/>
                            : <ApartmentsTable apartments={apartment.items} rooms={room.items}/>
                        }
                    </Grid>
                </Grid>
            </div>
        );
    }
}

const mapStateToProps = state =>
    ({
        housing:    state.abode.housing.item,
        apartment:  state.abode.apartment,
        room:       state.abode.room,
    });

const mapDispatchToProps = (dispatch, ownProps) =>
    ({
        fetchHousing: () => {
            const id = Number(ownProps.match.params.id);
            dispatch(abode.fetchHousing(id));
        },
        fetchRooms: () => {
            const id = Number(ownProps.match.params.id);
            dispatch(abode.fetchRooms({housing: id}));
        },
        fetchApartments: () => {
            const id = Number(ownProps.match.params.id);
            dispatch(abode.fetchApartments({housing: id}));
        },
        loadData: () => {
            dispatch(abode.fetchApartmentTypes());
            dispatch(abode.fetchRoomTypes());
        },
    });

export default connect(mapStateToProps, mapDispatchToProps)(Housing);