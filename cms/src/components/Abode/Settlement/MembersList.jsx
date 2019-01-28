import React from 'react';
import PropTypes from 'prop-types';
import {
    Typography,
    List,
    ListItem,
    ListItemText,
    TablePagination,
    Grid,
    TextField,
} from '@material-ui/core';
import MemberInfoListItem from "./MemberInfoListItem";
import map from "lodash/map";
import slice from "lodash/slice";
import f from "lodash/filter";

class MembersList extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            page: 0,
            rowsPerPage: 12,
            search: '',
        };
    }

    handleFitlerChange = (event) => this.setState({search: event.target.value, page: 0});
    handlePageChange = (event, page) => this.setState({page});

    getMembers = () => {
        const { members } = this.props;
        const { search } = this.state;
        let result = members;

        if (search) {
            result = f(result, i =>
                `${i.first_name} ${i.last_name} ${i.org_name}`.toLowerCase().indexOf(search.toLowerCase()) >= 0
            )
        }

        return result;
    };

    render() {
        const { MemberComponent, memberComponentProps } = this.props;
        const { page,rowsPerPage, search } = this.state;

        const members = this.getMembers();
        const members_list = slice(members, rowsPerPage*page, rowsPerPage*page + rowsPerPage);

        return (
            <React.Fragment>
                <Typography gutterBottom variant={`h5`}>Участники</Typography>
                <Grid container spacing={16}>
                    <Grid item xs={12}>
                        <TextField
                            required
                            label={"Поиск по ФИО или организации"}
                            margin={"dense"}
                            fullWidth
                            value={search}
                            variant={"outlined"}
                            name={'search'}
                            onChange={this.handleFilterChange}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        {members.length === 0
                            ? <Typography variant={`caption`} gutterBottom align={`center`}>Все участники заселены</Typography>
                            : <React.Fragment>
                                <List dense={true}>
                                    {map(members_list, mb =>
                                        <MemberComponent
                                            key={mb.id}
                                            extendInfo
                                            member={mb}
                                            place={null}
                                            {...memberComponentProps}
                                        />
                                    )}
                                </List>
                                <TablePagination
                                    style={{width: '100%'}}
                                    component={`div`}
                                    page={page}
                                    rowsPerPage={rowsPerPage}
                                    rowsPerPageOptions={[]}
                                    count={members.length}
                                    onChangePage={this.handlePageChange}
                                    labelDisplayedRows={({from, to, count}) => `${from}-${to} из ${count}`}
                                />
                            </React.Fragment>
                        }
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

MembersList.propTypes = {
    MemberComponent: PropTypes.func.isRequired,
    memberComponentProps: PropTypes.object,

    members: PropTypes.arrayOf(PropTypes.shape({
        id:             PropTypes.number.isRequired,
        first_name:     PropTypes.string.isRequired,
        last_name:      PropTypes.string.isRequired,
        org_name:       PropTypes.string.isRequired,
        room_type_id:   PropTypes.number.isRequired,
        neighbourhood:  PropTypes.string,
    })),
};

export default MembersList;