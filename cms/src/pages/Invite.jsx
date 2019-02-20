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
    Typography,
    Tooltip,
} from '@material-ui/core';
import { green, red } from '@material-ui/core/colors';
import AutorenewIcon from '@material-ui/icons/Autorenew';
import FabButton from '../components/utils/FabButton';
import InviteForm from "../components/Organization/InviteForm";
import participating from '../actions/participating';
import map from "lodash/map";
import ConfirmDialog from "../components/utils/ConfirmDialog";
import API from '../libs/api';
import Money from "../components/utils/Money";

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
    }

    componentDidMount() {
        this.update();
    }

    update = () => {
        const { fetchOrganizations, user } = this.props;
        const data = { invited_by: user.id };
        fetchOrganizations(data);
    };

    reInvite = (id) => request.get(`conference_organization/re_invite/${id}`);

    openForm = () => this.setState({form: {...this.state.form, open: true}});
    closeForm = () => this.setState({form: {...this.state.form, open: false}});

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
                    <Grid xs={12} item>
                        <Grid container justify={`space-between`} alignItems={`center`}>
                            <Grid item>
                                <Typography variant={`h4`}>Рассылка приглашений</Typography>
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
                                    <TableCell>Счета</TableCell>
                                    <TableCell>Статус</TableCell>
                                    <TableCell numeric>Повторная отправка</TableCell>
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
                                            <Typography variant={`caption`}>Участников: {item.total_members}</Typography>
                                        </TableCell>
                                        <TableCell>
                                            <div style={{whiteSpace: 'nowrap'}}><b>ИНН:</b> {item.inn}</div>
                                            <div style={{whiteSpace: 'nowrap'}}><b>КПП:</b> {item.kpp}</div>
                                        </TableCell>
                                        <TableCell>
                                            {item.invoices.length === 0 && 'Нет счета'}
                                            {map(item.invoices, (invoice, i) =>
                                                <div key={i} style={{ whiteSpace: 'nowrap', padding: `2px 0` }}>
                                                    Счет&nbsp;
                                                    <Tooltip
                                                        title={`${invoice.status === 3 ? `Оплачен` : `Не оплачен`} счет №${invoice.number} на сумму ${invoice.amount}₽`}
                                                    >
                                                        <span style={{
                                                            cursor: 'pointer',
                                                            color: invoice.status === 3 ? green[700] : red[700],
                                                            borderBottom: `1px dotted ${invoice.status === 3 ? green[700] : red[700]}`,
                                                        }}>№{invoice.number}</span>
                                                    </Tooltip>
                                                    &nbsp;на <Money value={invoice.amount}/>
                                                </div>
                                            )}
                                        </TableCell>
                                        <TableCell style={{whiteSpace: 'no-wrap'}}>
                                            {item.is_finish
                                                ? 'Зарегистрирована'
                                                : 'Отправлено приглашение'
                                            }
                                        </TableCell>
                                        <TableCell numeric>
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
        fetchOrganizations: (data) => {
            dispatch(participating.fetchConferenceOrganizations(data));
        },
    });

export default connect(mapStateToProps, mapDispatchToProps)(Invite);