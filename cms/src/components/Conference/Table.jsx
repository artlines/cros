import React from 'react';
import PropTypes from 'prop-types';
import {
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    IconButton,
    Grid,
    Paper,
    Tooltip,
} from '@material-ui/core';
import map from 'lodash/map';
import sortBy from 'lodash/sortBy';
import LinearProgress from '../utils/LinearProgress';
import {
    Edit as EditIcon,
    Archive as ArchiveIcon,
} from "@material-ui/icons";
import DateTime from "../utils/DateTime";
import MoreMenu from "../utils/MoreMenu";

class ConferenceTable extends React.PureComponent {

    render() {
        const { items, isFetching, onEdit } = this.props;

        return (
            <React.Fragment>
                <Grid container spacing={16}>
                    <Grid item xs={12}>
                        <LinearProgress show={isFetching}/>
                    </Grid>
                    <Grid item xs={12}>
                        <Paper>
                            <Table>
                                <TableHead>
                                    <TableRow>
                                        <TableCell>Год</TableCell>
                                        <TableCell>Начало регистрации</TableCell>
                                        <TableCell>Окончание регистрации</TableCell>
                                        <TableCell>Начало мероприятия</TableCell>
                                        <TableCell>Окончание мероприятия</TableCell>
                                        <TableCell>Лимит участников на конференцию</TableCell>
                                        <TableCell>Лимит участников на организацию</TableCell>
                                        <TableCell> </TableCell>
                                    </TableRow>
                                </TableHead>
                                <TableBody>
                                    {map(sortBy(items, 'year').reverse(), item =>
                                        <TableRow key={item.id}>
                                            <TableCell>{item.year}</TableCell>
                                            <TableCell><DateTime withTime value={item.reg_start}/></TableCell>
                                            <TableCell><DateTime withTime value={item.reg_finish}/></TableCell>
                                            <TableCell><DateTime withTime value={item.event_start}/></TableCell>
                                            <TableCell><DateTime withTime value={item.event_finish}/></TableCell>
                                            <TableCell>{item.users_limit_global || 'не указан'}</TableCell>
                                            <TableCell>{item.users_limit_by_org || 'не указан'}</TableCell>
                                            <TableCell style={{whiteSpace: 'nowrap'}}>
                                                <Tooltip title={`Изменить`}>
                                                    <IconButton onClick={() => onEdit(item.id)}><EditIcon/></IconButton>
                                                </Tooltip>
                                                <MoreMenu
                                                    items={[
                                                        {
                                                            title: `Архив`,
                                                            icon: ArchiveIcon,
                                                            href: `/cms/conference/${item.id}/archive`,
                                                        },
                                                    ]}
                                                />
                                            </TableCell>
                                        </TableRow>
                                    )}
                                </TableBody>
                            </Table>
                        </Paper>
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

ConferenceTable.propTypes = {
    items: PropTypes.arrayOf(
        PropTypes.shape({
            year: PropTypes.number.isRequired,
            users_limit_global: PropTypes.number,
            users_limit_by_org: PropTypes.number,
            reg_start: PropTypes.string.isRequired,
            reg_finish: PropTypes.string.isRequired,
            event_start: PropTypes.string.isRequired,
            event_finish: PropTypes.string.isRequired,
        }),
    ),
    isFetching: PropTypes.bool.isRequired,

    update: PropTypes.func.isRequired,
    onEdit: PropTypes.func.isRequired,
};

export default ConferenceTable;