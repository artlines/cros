import React from 'react';
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import {compose} from 'redux';
import {
    Paper,
    Typography,
    Divider,
    Grid,
} from '@material-ui/core';
import { withStyles } from '@material-ui/core/styles';
import map from 'lodash/map';
import find from 'lodash/find';
import MemberInfoChip from "./MemberInfoChip";

const styles = theme => ({
    apartBlock: {
        padding: theme.spacing.unit,
    },
    divider: {
        margin: `${theme.spacing.unit / 2}px 0`,
    },
});

class ApartmentBlock extends React.PureComponent {
    render() {
        const { apartment: { id, type_id, number, rooms }, classes, room_types, apartment_types } = this.props;

        if (apartment_types.length === 0 || room_types.length === 0) return null;

        const apartment_type = find(apartment_types, {id: type_id});

        return (
            <Paper className={classes.apartBlock}>
                <Grid container justify={`space-between`}>
                    <Grid item>
                        <Typography gutterBottom variant={`button`}>#{number}</Typography>
                    </Grid>
                    <Grid item>
                        <Typography gutterBottom variant={`caption`}>{apartment_type.title}</Typography>
                    </Grid>
                </Grid>
                {map(rooms, room => {
                    const room_type = find(room_types, {id: room.type_id});

                    return (
                        <React.Fragment key={room.id}>
                            <Divider className={classes.divider}/>
                            <Typography gutterBottom variant={`caption`}>{room_type.title}</Typography>
                            {map(room.places, place => {
                                const mb = place.member;

                                return (
                                    <MemberInfoChip
                                        key={mb.id}
                                        first_name={mb.first_name}
                                        last_name={mb.last_name}
                                    />
                                );
                            })}
                        </React.Fragment>
                    );
                })}
            </Paper>
        );
    }
}

ApartmentBlock.propTypes = {
    apartment: PropTypes.shape({
        id: PropTypes.number.isRequired,
        number: PropTypes.number.isRequired,
        type_id: PropTypes.number.isRequired,
        rooms: PropTypes.array.isRequired,
    }),

    classes: PropTypes.object.isRequired,

    room_types: PropTypes.arrayOf(
        PropTypes.shape({
            id: PropTypes.number.isRequired,
            title: PropTypes.string.isRequired,
            max_places: PropTypes.number.isRequired,
        }),
    ),

    apartment_types: PropTypes.arrayOf(
        PropTypes.shape({
            id: PropTypes.number.isRequired,
            title: PropTypes.string.isRequired,
            max_rooms: PropTypes.number.isRequired,
        }),
    ),
};

const mapStateToProps = state =>
    ({
        room_types: state.abode.room_type.items,
        apartment_types: state.abode.apartment_type.items,
    });

export default compose(
    connect(mapStateToProps),
    withStyles(styles),
)(ApartmentBlock);