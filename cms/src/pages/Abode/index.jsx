import React from "react";
import PropTypes from "prop-types";
import {
    Grid,
    Typography,
    Fab,
} from "@material-ui/core";
import { withTheme } from "@material-ui/core/styles";
import LibraryAddIcon from "@material-ui/icons/LibraryAdd";
import map from "lodash/map";
import HousingCard from "../../components/Abode/HousingCard";
import HousingForm from "../../components/Abode/HousingForm";

const housingData = [
    {
        id: 1,
        num_of_floors: 3,
        title: "Корпус 1А",
        description: "Реализация намеченных плановых заданий способствует повышению качества новых принципов формирования материально-технической и кадровой базы.",
        abode_info: [
            { room_type_id: 2, total: 200, busy: 5 },
            { room_type_id: 1, total: 100, busy: 52 },
            { room_type_id: 4, total: 300, busy: 147 },
        ],
    },
    {
        id: 2,
        num_of_floors: 4,
        title: "Корпус 1Б",
        description: "Прежде всего, социально-экономическое развитие обеспечивает широкому кругу (специалистов) участие в формировании модели развития.",
        abode_info: [
            { room_type_id: 2, total: 200, busy: 5 },
            { room_type_id: 1, total: 100, busy: 52 },
            { room_type_id: 4, total: 300, busy: 147 },
        ],
    },
    {
        id: 3,
        num_of_floors: 3,
        title: "Корпус 2",
        description: "Современные технологии достигли такого уровня, что укрепление и развитие внутренней структуры предоставляет широкие возможности для приоретизации разума над эмоциями.",
        abode_info: [
            { room_type_id: 2, total: 200, busy: 5 },
            { room_type_id: 1, total: 100, busy: 52 },
            { room_type_id: 4, total: 300, busy: 147 },
        ],
    },
];

class Abode extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            openHousingForm: false,
            //initialValues
        };
    }

    handleCloseHousingForm = () => this.setState({openHousingForm: false});

    render() {
        const { housing, theme } = this.props;
        const { openHousingForm } = this.state;

        console.log(`Abode::render`);

        return (
            <div>
                <HousingForm
                    open={openHousingForm}
                    onClose={this.handleCloseHousingForm}
                />
                <Grid container spacing={24}>
                    <Grid item xs={12}>
                        <Grid container spacing={0} justify={`space-between`}>
                            <Grid item>
                                <Typography variant={`h5`} component={`span`}>Корпуса для проживания</Typography>
                            </Grid>
                            <Grid item>
                                <Fab
                                    size="medium"
                                    variant="extended"
                                    color="primary"
                                    aria-label="Add"
                                    onClick={() => this.setState({openHousingForm: true})}
                                >
                                    <LibraryAddIcon style={{ marginRight: theme.spacing.unit }}/>
                                    Добавить корпус
                                </Fab>
                            </Grid>
                        </Grid>
                    </Grid>
                    {map(housing, h =>
                        <Grid key={h.id} item xs={12} sm={6} lg={4}>
                            <HousingCard housing={h}/>
                        </Grid>
                    )}
                    {housing.length === 0 &&
                        <Grid item xs={12}>
                            <Typography variant={`subtitle1`}>Нет данных</Typography>
                        </Grid>
                    }
                </Grid>
            </div>
        );
    }
}

Abode.propTypes = {
    /**
     * MuiTheme object
     */
    theme: PropTypes.object.isRequired,
    /**
     * Array of existed housing
     */
    housing: PropTypes.array.isRequired,
};

Abode.defaultProps = {
    housing: housingData,
};

export default withTheme()(Abode);