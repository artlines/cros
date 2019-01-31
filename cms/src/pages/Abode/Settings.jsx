import React from "react";
import {connect} from 'react-redux';
import {
    Grid,
} from "@material-ui/core";
import abode from '../../actions/abode';
import ParticipationClassTable from "../../components/Abode/Settings/ParticipationClassTable";
import ApartmentTypeTable from "../../components/Abode/Settings/ApartmentTypeTable";
import RoomTypeTable from "../../components/Abode/Settings/RoomTypeTable";

class Settings extends React.PureComponent {
    render() {
        return (
            <Grid container spacing={24}>
                <Grid item xs={12} sm={6}>
                    <ParticipationClassTable
                        load={this.props.fetchParticipationClasses}
                    />
                </Grid>
                <Grid item xs={12} sm={6}>
                    <ApartmentTypeTable
                        load={this.props.fetchApartmentTypes}
                    />
                </Grid>
                <Grid item xs={12}>
                    <RoomTypeTable
                        load={this.props.fetchRoomTypes}
                    />
                </Grid>
            </Grid>
        );
    }
}

const mapDispatchToProps = dispatch =>
    ({
        fetchParticipationClasses: () => dispatch(abode.fetchParticipationClasses()),
        fetchApartmentTypes: () => dispatch(abode.fetchApartmentTypes()),
        fetchRoomTypes: () => dispatch(abode.fetchRoomTypes()),
    });

export default connect(null, mapDispatchToProps)(Settings);