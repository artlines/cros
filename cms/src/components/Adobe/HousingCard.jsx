import React from 'react';
import PropTypes from 'prop-types';
import {
    Button,
    Card,
    CardContent,
    CardActions,
    Typography,
} from '@material-ui/core';


class HousingCard extends React.PureComponent {

    render() {

        return (
            <Card>
                <CardContent>
                    <Typography variant={`h5`}></Typography>
                </CardContent>
                <CardActions>
                    <Button size={`small`}>Шахматка</Button>
                </CardActions>
            </Card>
        );
    }

}

HousingCard.propTypes = {
    housing: PropTypes.shape({
        id:             PropTypes.number.isRequired,
        num_of_floors:  PropTypes.number.isRequired,
        title:          PropTypes.string.isRequired,
        description:    PropTypes.string.isRequired,
        adobe_info:     PropTypes.arrayOf(
            PropTypes.shape({
                room_type_id:   PropTypes.number.isRequired,
                total:          PropTypes.number.isRequired,
                busy:           PropTypes.number.isRequired,
            })
        ),
    }),
};

export default HousingCard;