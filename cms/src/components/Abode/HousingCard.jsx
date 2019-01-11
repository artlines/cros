import React from "react";
import PropTypes from "prop-types";
import {
    Button,
    Card,
    CardContent,
    CardActions,
    Grid,
    Typography,
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,

} from "@material-ui/core";
import map from "lodash/map";


class HousingCard extends React.PureComponent {

    render() {
        const { housing: { id, num_of_floors, title, description, abode_info } }  = this.props;

        return (
            <Card>
                <CardContent>
                    <Typography variant={`h5`} gutterBottom>{title}</Typography>
                    {description &&
                        <div>
                            <Typography variant={`subtitle1`}>Описание</Typography>
                            <Typography paragraph>{description}</Typography>
                        </div>
                    }
                    {/*<Typography variant={`subtitle2`}>Статистика номерного фонда</Typography>*/}
                    <Table>
                        <TableHead>
                            <TableRow>
                                <TableCell>Тип комнаты</TableCell>
                                <TableCell align={`right`}>Мест занято / Всего</TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {map(abode_info, item =>
                                <TableRow key={item.room_type_id}>
                                    <TableCell>{item.room_type_id}</TableCell>
                                    <TableCell align={`right`}>{item.busy} / {item.total}</TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </CardContent>
                <CardActions>
                    <Grid container justify={`space-between`}>
                        <Grid item>

                        </Grid>
                        <Grid item>
                            <Button>Шахматка</Button>
                        </Grid>
                    </Grid>
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
        abode_info:     PropTypes.arrayOf(
            PropTypes.shape({
                room_type_id:   PropTypes.number.isRequired,
                total:          PropTypes.number.isRequired,
                busy:           PropTypes.number.isRequired,
            })
        ),
    }),
};

export default HousingCard;