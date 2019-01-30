import React from "react";
import {connect} from 'react-redux';
import {
    Grid,
    Paper,
    Typography,
} from "@material-ui/core";
import abode from '../../actions/abode';
import ParticipationClassTable from "../../components/Abode/Settings/ParticipationClassTable";
import ApartmentTypeTable from "../../components/Abode/Settings/ApartmentTypeTable";
import RoomTypeTable from "../../components/Abode/Settings/RoomTypeTable";

class Settings extends React.Component {
    render() {
        return (
            <Grid container spacing={24}>
                <Grid item xs={12} sm={6}>
                    <Typography variant={`h4`} gutterBottom>Классы участия</Typography>
                    <Paper>
                        <ParticipationClassTable/>
                    </Paper>
                </Grid>
                <Grid item xs={12} sm={6}>
                    <Typography variant={`h4`} gutterBottom>Типы номеров</Typography>
                    <Paper>
                        <ApartmentTypeTable/>
                    </Paper>
                </Grid>
                <Grid item xs={12}>
                    <Typography variant={`h4`} gutterBottom>Типы комнат</Typography>
                    <Paper>
                        <RoomTypeTable/>
                    </Paper>
                </Grid>
            </Grid>
        );
    }
}

const mapStateToProps = state =>
    ({
        ...state.abode,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchParticipationClasses: () => dispatch(abode.fetchParticipationClasses()),
        fetchApartmentTypes: () => dispatch(abode.fetchApartmentTypes()),
        fetchRoomTypes: () => dispatch(abode.fetchRoomTypes()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Settings);