import React from "react";
import {connect} from 'react-redux';
import participating from '../actions/participating';
import OrganizationTable from '../components/Organization/Table';
import {
    TextField,
    Grid,
} from '@material-ui/core';
import isEqual from 'lodash/isEqual';

class Organizations extends React.Component {
    constructor(props) {
        super(props);

        this.searchTimeout = null;

        this.state = {
            query: {},
        };
    }

    componentDidUpdate(prevProps, prevState, prevContext) {
        const { query } = this.state;
        const { fetchOrganizations } = this.props;

        if (!isEqual(prevState.query, query)) {
            fetchOrganizations(query);
        }
    }

    update = (page, rowsPerPage, force = false) => {
        const { query } = this.state;
        const { fetchOrganizations } = this.props;
        let newQuery = {...query};

        newQuery['@limit'] = rowsPerPage;
        newQuery['@offset'] = rowsPerPage * page;

        if (!force) {
            this.setState({query: newQuery});
        } else {
            fetchOrganizations(newQuery);
        }
    };

    handleFilterChange = (event) => {
        let newQuery = {...this.state.query};

        if (!event.target.value) {
            delete(newQuery.search);
        } else {
            newQuery.search = event.target.value;
        }

        newQuery['@offset'] = 0;

        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.setState({query: newQuery});
        }, 350);
    };

    render() {
        const { fetchMembers, fetchComments, fetchInvoices } = this.props;

        return (
            <Grid container spacing={16}>
                <Grid item xs={12}>
                    <TextField
                        fullWidth
                        helperText={`Поиск по наименованию организации и ИНН`}
                        onChange={this.handleFilterChange}
                    />
                </Grid>
                <Grid item xs={12}>
                    <OrganizationTable
                        loadComments={fetchComments}
                        loadInvoices={fetchInvoices}
                        loadMembers={fetchMembers}
                        update={this.update}
                    />
                </Grid>
            </Grid>
        );
    }
}

const mapDispatchToProps = dispatch =>
    ({
        fetchOrganizations: (data = {}) => {
            dispatch(participating.fetchConferenceOrganizations(data))
        },
        fetchComments: (data = {}) => {
            dispatch(participating.fetchComments(data))
        },
        fetchInvoices: (data = {}) => {
            dispatch(participating.fetchInvoices(data))
        },
        fetchMembers: (data = {}) => {
            dispatch(participating.fetchMembers(data))
        },
    });

export default connect(null, mapDispatchToProps)(Organizations);