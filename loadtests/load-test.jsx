import http from 'k6/http';
import { check, sleep } from 'k6';
import { Trend, Rate } from 'k6/metrics';

const API_BASE = __ENV.API_BASE;

export let options = {
    vus: 5,
    duration: '20m',
    thresholds: {
        'http_req_duration': ['p(95)<200'],
        'http_req_failed': ['rate<0.1'],
    },
};

let responseTime = new Trend('response_time');
let errorRate   = new Rate('error_rate');

export default function () {
    let resPersonList = http.get(`${API_BASE}/person/list`, {
        insecureSkipTLSVerify: true,
    });
    responseTime.add(resPersonList.timings.duration);
    check(resPersonList, {
        'person/list → 200': (r) => r.status === 200,
    }) || errorRate.add(1);

    let personId = Math.floor(Math.random() * 10) + 1;
    let resPersonInspect = http.get(
        `${API_BASE}/person/inspect/${personId}`,
        { insecureSkipTLSVerify: true }
    );

    if (personId >= 6) {
        check(resPersonInspect, {
            'person/inspect ≥6 → 404': (r) => r.status === 404,
        });
    } else {
        let ok = check(resPersonInspect, { 'person/inspect <6 → 200': (r) => r.status === 200 });
        if (!ok) { errorRate.add(1); }
    }

    let resPetList = http.get(`${API_BASE}/pet/list`, {
        insecureSkipTLSVerify: true,
    });
    responseTime.add(resPetList.timings.duration);
    check(resPetList, {
        'pet/list → 200': (r) => r.status === 200,
    }) || errorRate.add(1);

    let petId = Math.floor(Math.random() * 10) + 1;
    let resPetInspect = http.get(
        `${API_BASE}/pet/inspect/${petId}`,
        { insecureSkipTLSVerify: true }
    );

    if (petId >= 6) {
        check(resPetInspect, {
            'pet/inspect ≥6 → 404': (r) => r.status === 404,
        });
    } else {
        let ok = check(resPetInspect, { 'pet/inspect <6 → 200': (r) => r.status === 200 });
        if (!ok) { errorRate.add(1); }
    }

    sleep(1);
}