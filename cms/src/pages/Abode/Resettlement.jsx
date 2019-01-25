import React from 'react';
import {connect} from 'react-redux';
import {compose} from 'redux';
import { DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import {
    Grid,
    Typography,
} from '@material-ui/core';
import map from 'lodash/map';
import ApartmentBlock from '../../components/Abode/Settlement/ApartmentBlock';
import MemberInfoChipSource from "../../containers/DragDrop/MemberInfoChipSource";
import RoomBlockTarget from "../../containers/DragDrop/RoomBlockTarget";
import abode from "../../actions/abode";
import resettlement from "../../actions/resettlement";
import LinearProgress from "../../components/utils/LinearProgress";

class Resettlement extends React.Component {
    constructor(props) {
        super(props);
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

    render() {
        const { apartments, members } = this.props;

        return (
            <div style={{maxHeight: '100%'}}>
                <LinearProgress/>
                <Grid container spacing={16}>
                    <Grid item xs={8} sm={8} lg={9}>
                        <Typography gutterBottom variant={`h5`}>Номера</Typography>
                        <Grid container spacing={16}>
                            {map(apartments.items, apart =>
                                <Grid key={apart.id} item xs={12} sm={6} md={4} xl={3}>
                                    <ApartmentBlock
                                        apartment={apart}
                                        RoomComponent={RoomBlockTarget}
                                        roomComponentProps={{
                                            MemberComponent: MemberInfoChipSource,
                                        }}
                                    />
                                </Grid>
                            )}
                        </Grid>
                    </Grid>
                    <Grid item xs={4} sm={4} lg={3}>
                        <div>
                            <Typography gutterBottom variant={`h5`}>Участники</Typography>
                            {map(members.items, mb =>
                                <MemberInfoChipSource
                                    key={mb.id}
                                    member={mb}
                                    extendInfo
                                />
                            )}
                        </div>
                    </Grid>
                </Grid>
            </div>
        );
    }
}

const mapStateToProps = state =>
    ({
        ...state.resettlement,
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