import React from 'react';
import {connect} from 'react-redux';
import {
    IconButton,
    Grid,
    Table,
    TableHead,
    TableBody,
    TableRow,
    TableCell,
    TextField,
    Typography,
    Tooltip,
} from '@material-ui/core';
import { green, red } from '@material-ui/core/colors';
import AutorenewIcon from '@material-ui/icons/Autorenew';
import FabButton from '../components/utils/FabButton';
import InviteForm from "../components/Organization/InviteForm";
import MembersModal from "../components/Organization/MembersModal";
import participating from '../actions/participating';
import map from "lodash/map";
import ConfirmDialog from "../components/utils/ConfirmDialog";
import API from '../libs/api';
import Money from "../components/utils/Money";
import LinearProgress from "../components/utils/LinearProgress";
import abode from "../actions/abode";

const request = new API();

class Invite extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            form: {
                open: false,
                initialValues: {},
            },
        };

        this.searchTimeout = null;
    }

    componentDidMount() {
        this.props.fetchRoomTypes();
        this.update();
    }

    update = (data) => {
        this.props.fetchOrganizations(data);
    };

    reInvite = (id) => request.get(`conference_organization/re_invite/${id}`);

    openForm = () => this.setState({form: {...this.state.form, open: true}});
    closeForm = () => this.setState({form: {...this.state.form, open: false}});

    handleSearchChange = event => {
        const search = event.target.value;
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.update({search});
        }, 350);
    };

    render() {
        const { organization: { items, isFetching } } = this.props;
        const { form } = this.state;

        return (
            <React.Fragment>
                <InviteForm
                    open={form.open}
                    initialValues={form.initialValues}
                    onClose={this.closeForm}
                    onSuccess={this.update}
                />
                <Grid container spacing={16}>
                    <Grid item xs={12}>
                        <LinearProgress show={isFetching}/>
                    </Grid>
                    <Grid xs={12} item>
                        <Grid container justify={`space-between`} alignItems={`center`}>
                            <Grid item>
                                <TextField
                                    name={`search`}
                                    fullWidth
                                    label={`Поиск`}
                                    helperText={`Поиск по наименованию организации, ИНН или ФИО сотрудника`}
                                    InputLabelProps={{shrink: true}}
                                    onChange={this.handleSearchChange}
                                />
                            </Grid>
                            <Grid item>
                                <FabButton
                                    title={`Создать приглашение`}
                                    onClick={this.openForm}
                                />
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid xs={12} item>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>ID</TableCell>
                                    <TableCell>Наименование</TableCell>
                                    <TableCell>Реквизиты</TableCell>
                                    <TableCell>Заказы</TableCell>
                                    <TableCell>Статус</TableCell>
                                    <TableCell align={`right`}>Повторная отправка</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {map(items, item =>
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            {item.id}
                                        </TableCell>
                                        <TableCell>
                                            {item.name}
                                            <Typography variant={`caption`}>{item.email}</Typography>
                                            <Typography variant={`caption`}>Менеджер: {item.invited_by}</Typography>
                                            <MembersModal
                                                organizationId={item.id}
                                                organizationName={item.name}
                                                trigger={
                                                    <Typography
                                                        component={`span`}
                                                        variant={`caption`}
                                                        style={{
                                                            cursor: 'pointer',
                                                            borderBottom: `1px dotted inherit`,
                                                        }}
                                                    >Участников: {item.total_members}</Typography>
                                                }
                                                update={this.props.fetchMembers}
                                                readOnly
                                            />
                                        </TableCell>
                                        <TableCell>
                                            <div style={{whiteSpace: 'nowrap'}}><b>ИНН:</b> {item.inn}</div>
                                            <div style={{whiteSpace: 'nowrap'}}><b>КПП:</b> {item.kpp}</div>
                                        </TableCell>
                                        <TableCell>
                                            {item.invoices.length === 0 && 'Нет счета'}
                                            {map(item.invoices, (invoice, i) =>
                                                <div key={i} style={{ whiteSpace: 'nowrap', padding: `2px 0` }}>
                                                    Заказ&nbsp;
                                                    <Tooltip
                                                        title={`${invoice.payed ? `Оплачен` : `Не оплачен`} счет заказа №${invoice.number} на сумму ${invoice.amount}₽ от ${invoice.date}`}
                                                    >
                                                        <span style={{
                                                            cursor: 'pointer',
                                                            color: invoice.payed ? green[700] : red[700],
                                                            borderBottom: `1px dotted ${invoice.payed ? green[700] : red[700]}`,
                                                        }}>№{invoice.number}</span>
                                                    </Tooltip>
                                                    &nbsp;на <Money value={invoice.amount}/><br/>от {invoice.date}
                                                    <Typography variant={`caption`}>{invoice.status_text}</Typography>
                                                </div>
                                            )}
                                        </TableCell>
                                        <TableCell style={{whiteSpace: 'no-wrap'}}>
                                            {item.is_finish ? 'Зарегистрирована' : 'Отправлено приглашение'}
                                        </TableCell>
                                        <TableCell align={`right`}>
                                            <ConfirmDialog
                                                title={`Повторная отправка приглашения`}
                                                text={`Вы уверены что хотите заного отправить письмо-приглашение?`}
                                                onConfirm={() => this.reInvite(item.id)}
                                                trigger={<IconButton><AutorenewIcon/></IconButton>}
                                            />
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

const mapStateToProps = state =>
    ({
        user: state.system.user,
        organization: state.participating.conference_organization,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchOrganizations: (data) => dispatch(participating.fetchConferenceOrganizations(data)),
        fetchMembers: (data) => dispatch(participating.fetchMembers(data)),
        fetchRoomTypes: () => dispatch(abode.fetchRoomTypes()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Invite);