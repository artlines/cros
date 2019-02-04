import React from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";
import {Link} from "react-router-dom";
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
import EditIcon from "@material-ui/icons/Edit";
import DeleteIcon from "@material-ui/icons/Delete";
import find from "lodash/find";
import map from "lodash/map";
import ConfirmDialog from "../utils/ConfirmDialog";

class HousingCard extends React.PureComponent {

    render() {
        const {
            housing: { id, num_of_floors, title, description, abode_info },
            room_types, onEdit, onDelete,
        }  = this.props;

        if (room_types.isFetching) return null;

        return (
            <Card>
                <CardContent>
                    <Grid container justify={`space-between`} alignItems={`center`}>
                        <Grid item>
                            <Typography variant={`h5`} gutterBottom>{title}</Typography>
                        </Grid>
                        <Grid item>
                            <Typography gutterBottom color={`textSecondary`} variant={`subtitle1`}>{num_of_floors} эт.</Typography>
                        </Grid>
                    </Grid>
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
                                    <TableCell>{find(room_types.items, {id: item.room_type_id}).title}</TableCell>
                                    <TableCell align={`right`}>{item.busy} / {item.total}</TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </CardContent>
                <CardActions>
                    <Grid container justify={`space-between`}>
                        <Grid item>
                            <Button onClick={() => onEdit(id)}><EditIcon/></Button>
                            <ConfirmDialog
                                trigger={<Button><DeleteIcon/></Button>}
                                onConfirm={() => onDelete(id)}
                            />
                        </Grid>
                        <Grid item>
                            <Link to={`/cms/abode/housing/${id}/apartments`}>
                                <Button>Номерной фонд</Button>
                            </Link>
                            <Link to={`/cms/abode/housing/${id}/resettlement`}>
                                <Button>Расселение</Button>
                            </Link>
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
    onEdit:     PropTypes.func.isRequired,
    onDelete:   PropTypes.func.isRequired,
    room_types: PropTypes.object.isRequired,
};

const mapStateToProps = state =>
    ({
        room_types: state.abode.room_type,
    });

export default connect(mapStateToProps)(HousingCard);