import React from 'react';
import PropTypes from 'prop-types';
import {
    Paper,
    Typography,
    Divider,
} from '@material-ui/core';
import { withStyles } from '@material-ui/core/styles';
import map from 'lodash/map';

const styles = theme => ({
    apartBlock: {
        padding: theme.spacing.unit,
    },
});

class ApartmentBlock extends React.PureComponent {
    render() {
        const { apartment: { id, number, rooms }, classes } = this.props;



        return (
            <Paper className={classes.apartBlock}>
                <Typography variant={`button`}>Номер #{number}</Typography>
                {map(rooms, room =>
                    <React.Fragment key={room.id}>
                        <Divider/>

                    </React.Fragment>
                )}
            </Paper>
        );
    }
}

ApartmentBlock.propTypes = {
    apartment: PropTypes.shape({
        id: PropTypes.number.isRequired,
        number: PropTypes.number.isRequired,
        rooms: PropTypes.array.isRequired,
    }),

    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(ApartmentBlock);