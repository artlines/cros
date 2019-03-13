import React from "react";
import PropTypes from 'prop-types';
import {connect} from 'react-redux';
import participating from '../actions/participating';
import abode from '../actions/abode';
import system from '../actions/system';
import OrganizationTable from '../components/Organization/Table';
import {
    TextField, MenuItem,
    Grid, FormControlLabel, Switch, FormHelperText, FormControl,
} from '@material-ui/core';
import isArray from 'lodash/isArray';
import isEqual from 'lodash/isEqual';
import isNumber from 'lodash/isNumber';
import find from "lodash/find";
import map from "lodash/map";
import OrganizationForm from '../components/Organization/Form';
import FabButton from '../components/utils/FabButton';
import SuggestingSelectField from "../components/utils/SuggestingSelectField";

const stages = [
    { value: 1, text: 'Приглашение отправлено' },
    { value: 2, text: 'Регистрация завершена' },
    { value: 3, text: 'Участники расселены' },
    { value: 4, text: 'Счет отправлен' },
    { value: 5, text: 'Счет оплачен' },
    { value: 6, text: 'Счет отменен' },
];

class Organizations extends React.Component {
    constructor(props) {
        super(props);

        this.searchTimeout = null;

        this.state = {
            query: {},
            form: {
                open: false,
                initialValues: {},
            },
            filter: {
                stage: 0,
            },
        };
    }

    componentDidMount() {
        this.props.fetchRoomTypes();
        this.props.fetchManagers();
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

        rowsPerPage && (newQuery['@limit'] = rowsPerPage);
        rowsPerPage && isNumber(page) && (newQuery['@offset'] = rowsPerPage * page);

        if (!force) {
            this.setState({query: newQuery});
        } else {
            fetchOrganizations(newQuery);
        }
    };

    handleFilterChange = field => event => {
        let newQuery = {...this.state.query};
        const currentFilter = {...this.state.filter};
        const value = isArray(event) ? map(event, i => i.value) : (event.target.checked ? true : event.target.value);

        if (!value) {
            delete(newQuery[field]);
        } else {
            newQuery[field] = value;
        }

        newQuery['@offset'] = 0;

        this.setState({ filter: { ...currentFilter, [field]: value } });

        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.setState({query: newQuery});
        }, 350);
    };

    openForm = (id) => {
        const { items } = this.props.organizations;
        this.setState({
            form: {
                ...this.state.form,
                open: true,
                initialValues: id ? find(items, {id}) : {},
            }
        });
    };
    closeForm = () => this.setState({form: {...this.state.form, open: false}});

    render() {
        const { fetchMembers, fetchComments, fetchInvoices, managers, organizations } = this.props;
        const { form, filter } = this.state;

        return (
            <React.Fragment>
                <OrganizationForm
                    open={form.open}
                    initialValues={form.initialValues}
                    onClose={this.closeForm}
                    onSuccess={() => this.update(null, null, true)}
                />
                <Grid container spacing={16}>
                    <Grid item xs={12} sm={6} lg={3}>
                        <TextField
                            fullWidth
                            label={`Поиск`}
                            helperText={`Поиск по наименованию организации, ИНН или ФИО сотрудника`}
                            onChange={this.handleFilterChange(`search`)}
                            InputLabelProps={{shrink: true}}
                        />
                    </Grid>
                    <Grid item xs={12} sm={6} lg={3}>
                        <SuggestingSelectField
                            options={map(managers, i => ({ value: i.id, label: `${i.first_name} ${i.last_name}` }))}
                            onChange={this.handleFilterChange(`invited_by[]`)}
                            isSearchable
                            isMulti
                            placeholder={`Начните вводить имя`}
                            label={`Ответственный менеджер`}
                            fullWidth
                        />
                    </Grid>
                    <Grid item xs={12} sm={6} lg={3}>
                        <TextField
                            label={"Этап"}
                            fullWidth
                            onChange={this.handleFilterChange(`stage`)}
                            select={true}
                            InputLabelProps={{shrink: true}}
                            value={filter.stage}
                        >
                            <MenuItem value={0}>Все</MenuItem>
                            {stages.map(i => <MenuItem key={i.value} value={i.value}>{i.text}</MenuItem>)}
                        </TextField>
                    </Grid>
                    <Grid item xs={12} sm={6} lg={3}>
                        <FormControl>
                            <FormControlLabel
                                label={"Есть комментарии"}
                                control={
                                    <Switch onChange={this.handleFilterChange('with_comments')} />
                                }
                            />
                            <FormHelperText>Имеются комментарии по организации</FormHelperText>
                        </FormControl>
                    </Grid>
                    <Grid item xs={12} sm={6} lg={3}>
                        <FormControl>
                            <FormControlLabel
                                label={"Нет ответственного"}
                                control={
                                    <Switch onChange={this.handleFilterChange('without_manager')} />
                                }
                            />
                            <FormHelperText>Не указан ответственный менеджер</FormHelperText>
                        </FormControl>
                    </Grid>
                    <Grid item xs={12}>
                        <Grid container justify={`flex-end`}>
                            <Grid item>
                                <FabButton title={`Добавить организацию`} onClick={this.openForm}/>
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid item xs={12}>
                        <OrganizationTable
                            {...organizations}
                            loadComments={fetchComments}
                            loadInvoices={fetchInvoices}
                            loadMembers={fetchMembers}
                            update={this.update}
                            onEdit={this.openForm}
                        />
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

Organizations.propTypes = {
    organizations: PropTypes.shape({
        isFetching: PropTypes.bool.isRequired,
        total_count: PropTypes.number.isRequired,
        items: PropTypes.arrayOf(
            PropTypes.shape({
                id:                 PropTypes.number.isRequired,
                name:               PropTypes.string.isRequired,
                hidden:             PropTypes.bool,
                inn:                PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
                kpp:                PropTypes.oneOfType([PropTypes.number, PropTypes.string]),
                city:               PropTypes.string,
                address:            PropTypes.string,
                requisites:         PropTypes.string,
                total_members:      PropTypes.number.isRequired,
                in_room_members:    PropTypes.number.isRequired,
                comments_count:     PropTypes.number.isRequired,
                invoices_count:     PropTypes.number.isRequired,
                invoices_payed:     PropTypes.number.isRequired,
                invited_by:         PropTypes.string,
            }),
        ),
    }),
};

const mapStateToProps = state =>
    ({
        managers: state.system.managers.items,
        organizations: state.participating.conference_organization,
    });

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
        fetchRoomTypes: () => dispatch(abode.fetchRoomTypes()),
        fetchManagers: () => dispatch(system.fetchManagers()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Organizations);