flowchart TD

Page["Page"]

Component["Components"]

Service["API Service"]

Symfony["Symfony API"]

Page --> Component

Component --> Service

Service --> Symfony