import React from "react";
import {connect} from 'react-redux';
import {
    Paper,
    Typography,
    Grid,
} from "@material-ui/core";
import RoomsSummaryInformation from "../components/Abode/RoomsSummaryInformation";
import abode from "../actions/abode";

class Home extends React.Component {
    constructor(props) {
        super(props);

        this.updateInterval = null;
    }

    componentDidMount() {
        const { roles } = this.props;

        if (roles.includes('ROLE_SETTLEMENT_MANAGER')) {
            this.props.fetchRoomsSummaryInformation();
            this.updateInterval = setInterval(() => {
                this.props.fetchRoomsSummaryInformation();
            }, 60000);
        }
    }

    componentWillUnmount() {
        clearInterval(this.updateInterval);
    }

    render() {
        const { roles, rooms_summary_information } = this.props;

        return (
            <Grid container spacing={16}>
                {roles.includes('ROLE_SETTLEMENT_MANAGER') &&
                <Grid item xs={12}>
                    <Typography gutterBottom variant={`h5`}>Сводная информация по комнатам</Typography>
                    <Paper>
                        <RoomsSummaryInformation {...rooms_summary_information}/>
                    </Paper>
                </Grid>
                }
            </Grid>
        );
    }
}

const mapStateToProps = state =>
    ({
        rooms_summary_information: state.abode.rooms_summary_information,
        roles: state.system.user.roles,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchRoomsSummaryInformation: () => dispatch(abode.fetchRoomsSummaryInformation()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Home);