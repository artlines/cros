import React from "react";
import {connect} from 'react-redux';
import {
    Grid,
    Typography,
    Divider,
} from "@material-ui/core";
import map from "lodash/map";
import AddButton from '../../components/utils/FabButton';
import HousingCard from "../../components/Abode/HousingCard";
import HousingForm from "../../components/Abode/HousingForm";
import find from 'lodash/find';
import sortBy from 'lodash/sortBy';
import abode from '../../actions/abode';
import API from '../../libs/api';
import LinearProgress from "../../components/utils/LinearProgress";

const api = new API();

class Abode extends React.Component {
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
        this.loadData();
    }

    loadData = () => {
        this.props.fetchHousings();
    };

    handleCloseHousingForm = () => this.setState({form: {...this.state.form, open: false}});

    openAddForm = () => {
        this.setState({
            form: {
                ...this.state.form,
                open: true,
                initialValues: {},
            },
        });
    };

    openEditForm = (id) => {
        const { housing } = this.state;

        this.setState({
            form: {
                ...this.state.form,
                open: true,
                initialValues: find(housing, {id}),
            }
        });
    };

    deleteItem = id => {
        this.setState({isFetching: true});
        api.delete(`housing/${id}`)
            .then(this.loadData)
            .catch(err => console.log(err))
    };

    render() {
        const { form: { open, initialValues } } = this.state;
        const { housing: { items, isFetching } } = this.props;

        console.log(`Housing index`, items);

        return (
            <div>
                <LinearProgress show={isFetching}/>
                <HousingForm
                    open={open}
                    initialValues={initialValues}
                    onClose={this.handleCloseHousingForm}
                    onSuccess={this.loadData}
                />
                <Grid container spacing={24}>
                    <Grid item xs={12}>
                        <Grid container spacing={0} justify={`space-between`} alignItems={`center`}>
                            <Grid item>
                                <Typography variant={`h5`} component={`span`}>Корпуса для проживания</Typography>
                            </Grid>
                            <Grid item>
                                <AddButton onClick={this.openAddForm} title={`Добавить корпус`}/>
                            </Grid>
                        </Grid>
                    </Grid>
                    {map(sortBy(items, 'title'), h =>
                        <Grid key={h.id} item xs={12} sm={6} lg={4}>
                            <HousingCard housing={h} onEdit={this.openEditForm} onDelete={this.deleteItem} update={this.loadData}/>
                        </Grid>
                    )}
                    {items.length === 0 &&
                        <Grid item xs={12}>
                            <Typography variant={`subtitle1`}>Нет данных</Typography>
                        </Grid>
                    }
                </Grid>
            </div>
        );
    }
}

const mapStateToProps = state =>
    ({
        housing: state.abode.housing,
    });

const mapDispatchToProps = dispatch =>
    ({
        fetchHousings: () => dispatch(abode.fetchHousings()),
    });

export default connect(mapStateToProps, mapDispatchToProps)(Abode);