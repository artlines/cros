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
    Tooltip,
} from "@material-ui/core";
import {
    Edit as EditIcon,
    Delete as DeleteIcon,
    Lock as LockIcon,
} from "@material-ui/icons";
import map from "lodash/map";
import sortBy from "lodash/sortBy";
import ConfirmDialog from "../utils/ConfirmDialog";
import ReservedPlacesModal from './ReservedPlacesModal';

class HousingCard extends React.PureComponent {

    render() {
        const {
            housing: { id, num_of_floors, title, description, abode_info },
            update, onEdit, onDelete,
        }  = this.props;

        console.log(this.props);

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

                            <ReservedPlacesModal
                                trigger={
                                    <Tooltip title={`Резервирование`}>
                                        <IconButton><LockIcon/></IconButton>
                                    </Tooltip>
                                }
                                items={abode_info}
                                onSuccessSubmit={update}
                            />
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
                room_type_id:       PropTypes.number.isRequired,
                room_type_title:    PropTypes.string.isRequired,
                reserved:           PropTypes.number.isRequired,
                busy:               PropTypes.number.isRequired,
                total:              PropTypes.number.isRequired,
            })
        ),
    }),
    update:     PropTypes.func.isRequired,
    onEdit:     PropTypes.func.isRequired,
    onDelete:   PropTypes.func.isRequired,
};

export default HousingCard;