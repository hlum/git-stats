import axios, { AxiosResponse } from "axios";

const GITHUB_API_URL = "https://api.github.com/graphql";

export interface GraphQLRequest {
	query: string;
	variables: Record<string, any>;
}

export async function request(data: GraphQLRequest, header: Record<string, string>): Promise<AxiosResponse> {
	return axios.post(GITHUB_API_URL, data, {
		headers: {
			"Content-Type": "application/json",
			...header,
		},
	});
}
