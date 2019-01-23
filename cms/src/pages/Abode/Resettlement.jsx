import React from 'react';
import {
    Grid,
    Typography,
    Divider,
} from '@material-ui/core';
import map from 'lodash/map';
import MemberInfoChip from "../../components/Abode/MemberInfoChip";

class Resettlement extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const { room, member } = this.props;

        return (
            <Grid container spacing={16}>
                <Grid item xs={6} sm={8} md={8} lg={10}>
                    <Typography gutterBottom variant={`h5`}>Комнаты</Typography>
                </Grid>
                <Grid item xs={6} sm={4} md={4} lg={2}>
                    <div>
                        <Typography gutterBottom variant={`h5`}>Участники</Typography>
                        {map(member.items, mb =>
                            <MemberInfoChip
                                key={mb.id}
                                first_name={mb.first_name}
                                last_name={mb.last_name}
                                org_name={mb.org_name}
                            />
                        )}
                    </div>
                </Grid>
            </Grid>
        );
    }
}

Resettlement.defaultProps = {
    room: {
        isFetching: false,
        count: 0,
        items: [
            {
                id: 1,
                number: 1001,
                max_places: 4,
                type_id: 1,
                places: [
                    {
                        id: 12,
                        member: [
                            { id: 321, first_name: 'Вася', last_name: 'Пупкин', org_name: 'ООО ААА', room_type_id: 1 },
                        ],
                        approved: 0,
                    },
                    {
                        id: 23,
                        member: [
                            { id: 321, first_name: 'Петя', last_name: 'Петров', org_name: 'ООО BBB', room_type_id: 1 },
                        ],
                        approved: 0,
                    },
                ]
            },
            {
                id: 2,
                number: 1002,
                max_places: 2,
                type_id: 2,
                places: [
                    {
                        id: 12,
                        member: [
                            { id: 321, first_name: 'Петр', last_name: 'Петин', org_name: 'ООО ААА', room_type_id: 2 },
                        ],
                        approved: 0,
                    },
                    {
                        id: 23,
                        member: [
                            { id: 321, first_name: 'Пуп', last_name: 'Васин', org_name: 'ООО DDD', room_type_id: 2 },
                        ],
                        approved: 0,
                    },
                ]
            },
        ],
    },
    member: {
        isFetching: false,
        count: 0,
        items: [
            { id: 43, first_name: 'Иван', last_name: 'Петров', org_name: 'ИП ЫЫ', room_type_id: 2 },
            { id: 16, first_name: 'Антон', last_name: 'Черкашин', org_name: 'ООО Рога', room_type_id: 2 },
            { id: 26, first_name: 'Игорь', last_name: 'Бошмак', org_name: 'ООО Рога и Рога и Рога и Копыта и Рога', room_type_id: 2 },
        ],
    },
};

export default Resettlement;