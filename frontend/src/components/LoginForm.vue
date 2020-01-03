<template>
    <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="4">
                <v-card class="elevation-12">
                    <v-toolbar color="primary" dark flat>
                        <v-toolbar-title>Авторизация</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field label="E-Mail" name="login" prepend-icon="mdi-account" type="text"/>
                            <v-text-field id="password" label="Пароль" name="password" prepend-icon="mdi-lock"
                                          type="password"/>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn outlined color="indigo">Регистрация</v-btn>
                        <v-spacer/>
                        <v-btn color="primary">Войти</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
  export default {
    name: "LoginForm"
  }
</script>
