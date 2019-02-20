import React from "react";
import PropTypes from "prop-types";
import {connect} from "react-redux";
import {Link} from "react-router-dom";
import {
    Button,
    IconButton,
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
import map from "lodash/map";
import sortBy from "lodash/sortBy";
import ConfirmDialog from "../utils/ConfirmDialog";

class HousingCard extends React.PureComponent {

    render() {
        const {
            housing: { id, num_of_floors, title, description, abode_info },
            onEdit, onDelete,
        }  = this.props;

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
                                <TableCell numeric>Мест в резерве / Занято / Всего</TableCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {map(sortBy(abode_info, 'room_type_title'), item =>
                                <TableRow key={item.room_type_id}>
                                    <TableCell>{item.room_type_title}</TableCell>
                                    <TableCell numeric>{item.reserved} / {item.busy} / {item.total}</TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </CardContent>
                <CardActions>
                    <Grid container justify={`space-between`}>
                        <Grid item>
                            <IconButton onClick={() => onEdit(id)}><EditIcon/></IconButton>
                            <ConfirmDialog
                                trigger={<IconButton><DeleteIcon/></IconButton>}
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
};

export default HousingCard;