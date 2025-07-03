# Activity Diagram - VehicleFlow Booking Process

## Overview

Diagram aktivitas ini menggambarkan alur lengkap proses pemesanan kendaraan dalam sistem VehicleFlow, mulai dari pembuatan permintaan hingga penyelesaian pemesanan dengan sistem persetujuan bertingkat.

## Main Booking Process Flow

```mermaid
graph TD
    A[User Login] --> B{Check User Role}
    B -->|Requester| C[Access Booking Form]
    B -->|Admin| D[Access Admin Dashboard]
    B -->|Approver| E[Access Approval Dashboard]
    
    C --> F[Fill Booking Details]
    F --> G[Validate Form Data]
    G -->|Invalid| H[Show Validation Errors]
    H --> F
    G -->|Valid| I[Submit Booking Request]
    
    I --> J[Generate Booking Number]
    J --> K[Save to Database]
    K --> L[Create Level 1 Approval Record]
    L --> M[Create Level 2 Approval Record]
    M --> N[Send Notification to Level 1 Approver]
    N --> O[Update Booking Status to 'Pending']
    
    O --> P[Level 1 Approval Process]
    P --> Q{Level 1 Decision}
    Q -->|Reject| R[Update Status to 'Rejected']
    Q -->|Approve| S[Update Status to 'Approved Level 1']
    
    R --> T[Send Rejection Notification]
    T --> U[Log Activity]
    U --> V[End Process]
    
    S --> W[Send Notification to Level 2 Approver]
    W --> X[Level 2 Approval Process]
    X --> Y{Level 2 Decision}
    Y -->|Reject| R
    Y -->|Approve| Z[Update Status to 'Approved']
    
    Z --> AA[Assign Vehicle and Driver]
    AA --> BB[Send Approval Notification]
    BB --> CC[Log Activity]
    CC --> DD[Booking Ready for Use]
    DD --> EE[End Process]
