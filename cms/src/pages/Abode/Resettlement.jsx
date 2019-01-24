import React from 'react';
import {connect} from 'react-redux';
import {compose} from 'redux';
import { DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import {
    Grid,
    Typography,
} from '@material-ui/core';
import map from 'lodash/map';
import ApartmentBlock from '../../components/Abode/Settlement/ApartmentBlock';
import MemberInfoChipSource from "../../containers/DragDrop/MemberInfoChipSource";
import RoomBlockTarget from "../../containers/DragDrop/RoomBlockTarget";
import abode from "../../actions/abode";

class Resettlement extends React.Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        this.props.fetchRoomTypes();
        this.props.fetchApartmentTypes();
    }

    render() {
        const { apartment, member } = this.props;

        return (
            <Grid container spacing={16}>
                <Grid item xs={8} sm={8} lg={9}>
                    <Typography gutterBottom variant={`h5`}>Номера</Typography>
                    {apartment.items.length > 0 &&
                        <Grid container spacing={16}>
                            {map(apartment.items, apart =>
                                <Grid key={apart.id} item xs={12} sm={6} md={4} xl={3}>
                                    <ApartmentBlock
                                        apartment={apart}
                                        RoomComponent={RoomBlockTarget}
                                        roomComponentProps={{
                                            MemberComponent: MemberInfoChipSource,
                                        }}
                                    />
                                </Grid>
                            )}
                        </Grid>
                    }
                </Grid>
                <Grid item xs={4} sm={4} lg={3}>
                    <div>
                        <Typography gutterBottom variant={`h5`}>Участники</Typography>
                        {map(member.items, mb =>
                            <MemberInfoChipSource
                                key={mb.id}
                                member={mb}
                                extendInfo
                            />
                        )}
                    </div>
                </Grid>
            </Grid>
        );
    }
}

Resettlement.defaultProps = {
    apartment: {
        isFetching: false,
        count: 0,
        items: [
            {
                id: 1,
                number: 1001,
                type_id: 1,
                rooms: [
                    {
                        id: 1,
                        type_id: 1,
                        places: [
                            {
                                id: 12,
                                member: { id: 1, first_name: 'Евгенийнийний', last_name: 'Начуйченкококок', org_name: 'ООО ААА', room_type_id: 1 },
                                approved: 0,
                            },
                            {
                                id: 23,
                                member: { id: 2, first_name: 'Петя', last_name: 'Петров', org_name: 'ООО BBB', room_type_id: 1 },
                                approved: 0,
                            },
                        ]
                    },
                    {
                        id: 2,
                        type_id: 2,
                        places: []
                    },
                ],
            },
            {
                id: 2,
                number: 1002,
                type_id: 2,
                rooms: [
                    {
                        id: 1,
                        type_id: 1,
                        places: [
                            {
                                id: 12,
                                member: { id: 5, first_name: 'Вася', last_name: 'Пупкин', org_name: 'ООО ААА', room_type_id: 1 },
                                approved: 0,
                            },
                            {
                                id: 23,
                                member: { id: 6, first_name: 'Петя', last_name: 'Петров', org_name: 'ООО BBB', room_type_id: 1 },
                                approved: 0,
                            },
                        ]
                    },
                    {
                        id: 2,
                        type_id: 2,
                        places: [
                            {
                                id: 23,
                                member: { id: 22, first_name: 'Пуп', last_name: 'Васин', org_name: 'ООО DDD', room_type_id: 2 },
                                approved: 0,
                            },
                        ]
                    },
                ],
            },
            {
                id: 3,
                number: 1003,
                type_id: 1,
                rooms: [
                    {
                        id: 1,
                        type_id: 1,
                        places: [
                            {
                                id: 12,
                                member: { id: 33, first_name: 'Вася', last_name: 'Пупкин', org_name: 'ООО ААА', room_type_id: 1 },
                                approved: 0,
                            },
                        ]
                    },
                    {
                        id: 2,
                        type_id: 2,
                        places: [
                            {
                                id: 12,
                                member: { id: 55, first_name: 'Петр', last_name: 'Петин', org_name: 'ООО ААА', room_type_id: 2 },
                                approved: 0,
                            },
                        ]
                    },
                ],
            },
            {
                id: 4,
                number: 1004,
                type_id: 2,
                rooms: [
                    {
                        id: 1,
                        type_id: 1,
                        places: []
                    },
                    {
                        id: 2,
                        type_id: 2,
                        places: [
                            {
                                id: 12,
                                member: { id: 354, first_name: 'Петр', last_name: 'Петин', org_name: 'ООО ААА', room_type_id: 2 },
                                approved: 0,
                            },
                        ]
                    },
                ],
            },
            {
                id: 5,
                number: 1005,
                type_id: 1,
                rooms: [
                    {
                        id: 1,
                        type_id: 1,
                        places: []
                    },
                    {
                        id: 2,
                        type_id: 2,
                        places: []
                    },
                ],
            },
        ],
    },
    member: {
        isFetching: false,
        count: 0,
        items: [
            { id: 43, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
            { id: 16, first_name: 'Антон', last_name: 'Черкашин', org_name: 'ООО Рога', room_type_id: 1 },
            { id: 26, first_name: 'Игорь', last_name: 'Бошмак', org_name: 'ООО Рога и Рога и Рога и Копыта и Рога', room_type_id: 2 },
        ],
    },
};

const mapDispatchToProps = dispatch =>
    ({
        fetchRoomTypes: () => dispatch(abode.fetchRoomTypes()),
        fetchApartmentTypes: () => dispatch(abode.fetchApartmentTypes()),
    });

export default compose(
    connect(null, mapDispatchToProps),
    DragDropContext(HTML5Backend),
)(Resettlement);